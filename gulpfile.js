const autoprefixer = require('autoprefixer');
const bs = require('browser-sync').create();
const del = require('del');
const deleteEmpty = require('delete-empty');
const gulp = require('gulp');
const cached = require('gulp-cached');
const cleanCSS = require('gulp-clean-css');
const fileInclude = require('gulp-file-include');
const htmlmin = require('gulp-htmlmin');
const imagemin = require('gulp-imagemin');
const notify = require('gulp-notify');
const plumber = require('gulp-plumber');
const postcss = require('gulp-postcss');
const pug = require('gulp-pug');
const revAll = require('gulp-rev-all');
const revDel = require('gulp-rev-delete-original');
const revReplace = require('gulp-rev-replace');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const stylus = require('gulp-stylus');
const watch = require('gulp-watch');
const mozjpeg = require('imagemin-mozjpeg');
const pngquant = require('imagemin-pngquant');
const packageImporter = require('node-sass-package-importer');
const path = require('path');
const pixrem = require('pixrem');
const reporter = require('postcss-reporter');
const runSequence = require('run-sequence');
const stylelint = require('stylelint');
const webpack = require('webpack');
const webpackStream = require('webpack-stream');
const replace = require('gulp-replace');    // 文字列置換
// const sassGlob = require('gulp-sass-glob'); // 一括でSCSSを指定する
const sassGlob = require('gulp-sass-glob-use-forward');         // ディレクトリ毎にscssをforwardできるようにする
const env = require('node-env-file');
const pxtorem = require('postcss-pxtorem'); // pxをremの単位に変換

sass.compiler = require('sass');
env('.env', { raise: false });

const dir = {
  src: './src',
  dist: './htdocs',
  // replace: '/htdocs',         // 画像やリンクなどのパスから除外するもの
  wp: process.env.THEME_PATH,   // .envで設定するテーマディレクトリへのパス
  wpImg: process.env.IMG_PATH,  // .envで設定する画像ディレクトリへのパス
};
const setting = {
  vhost: process.env.VHOST,   // .envで設定
  enableVhost: true,         // true:WP構築時 ／ false:静的コーディング時
  external: true
};

const DEBUG = !process.argv.includes('build');

const cssProcessors = [
  // pixrem({
  //   atrules: true
  // }),
  autoprefixer({
    grid: true,
    cascade: false
  }),
  pxtorem({
    rootValue: 10,
    unitPrecision: 5,
    propList: ['*'],
    selectorBlackList: [],
    mediaQuery: false,
    minPixelValue: 2,
    exclude: /node_modules/i,
    replace: false
  })
];

const cleanLevel = {
  dev: 0,
  prod: {
    2: {
      mergeMedia: false,
      overrideProperties: false
    }
  }
};

const cleanConf = {
  format: 'beautify',
  level: cleanLevel.dev
};

/**
 * Build Subtasks
 */
gulp.task('browser-sync', () => {
  let bsOption = { notify: false }
  if ( setting.external ) bsOption['open'] = 'external';
  if ( setting.enableVhost && setting.vhost ) bsOption['proxy'] = setting.vhost;
  else bsOption['server'] = { baseDir: dir.dist };
  return bs.init(bsOption);
});

gulp.task('clean:dist', callback => {
  return del([
    dir.dist + '/**/*.*',
    '!' + dir.dist + dir.wp + '/_assets/css/**/*.*',
    '!' + dir.dist + dir.wp + '/_assets/js/**/*.*',
    '!' + dir.dist + dir.wpImg + '/**/*.*',
    '!' + dir.dist + dir.wp + '/**/*.php',
    '!' + dir.dist + '/**/*.html'
  ], callback);
});

gulp.task('clean:styles', callback => {
  return del([
    dir.dist + dir.wp + '/_assets/css/**/*.*',
    dir.dist + dir.wp + '/_assets/sourcemaps/**/*.css.map'
  ], callback);
});

gulp.task('clean:scripts', callback => {
  return del([
    dir.dist + dir.wp + '/_assets/js/**/*.*',
    dir.dist + dir.wp + '/_assets/sourcemaps/**/*.js.map'
  ], callback);
});

gulp.task('clean:images', callback => {
  return del([dir.dist + dir.wpImg + '/**/*.*'], callback);
});

gulp.task('clean:html', callback => {
  return del([dir.dist + '/**/*.html'], callback);
});

gulp.task('clean:php', callback => {
  return del([dir.dist + dir.wp + '/**/*.php'], callback);
});

gulp.task('delete-empty', () => {
  return deleteEmpty.sync(dir.dist);
});

gulp.task('copy:src', ['delete-empty'], () => {
  return gulp.src([
    dir.src + '/**/*.*',
    '!' + dir.src + '/_assets/scss/**/*.scss',
    '!' + dir.src + '/_assets/styl/**/*.styl',
    '!' + dir.src + '/_assets/js/**/*.js',
    '!' + dir.src + '/_assets/images/**/*.*',
    '!' + dir.src + '/**/*.php',
    '!' + dir.src + '/**/*.pug',
    '!' + dir.src + '/**/*.html'
  ], {nodir: true})
    .pipe(gulp.dest(dir.dist))
    .pipe(bs.stream());
});

gulp.task('update', () => {
  return gulp.src([
    dir.src + '/**/*.*',
    '!' + dir.src + '/_assets/scss/**/*.scss',
    '!' + dir.src + '/_assets/styl/**/*.styl',
    '!' + dir.src + '/_assets/js/**/*.js',
    '!' + dir.src + '/_assets/images/**/*.*',
    '!' + dir.src + '/**/*.php',
    '!' + dir.src + '/**/*.pug',
    '!' + dir.src + '/**/*.html'
  ], {nodir: true})
    .pipe(cached('update'))
    .pipe(gulp.dest(dir.dist))
    .pipe(bs.stream());
});

gulp.task('copy', callback => {
  runSequence('clean:dist', 'copy:src', callback);
});

gulp.task('stylelint', ['clean:styles'], () => {
  return gulp.src([dir.src + '/_assets/scss/**/*.scss'])
    .pipe(plumber({errorHandler: notify.onError('<%= error.message %>')}))
    .pipe(postcss([
      stylelint({fix: true}),
      reporter()
    ], {syntax: require('postcss-scss')}))
    .pipe(cached('stylelint'))
    .pipe(gulp.dest(dir.src + '/_assets/scss'));
});

gulp.task('styles', ['stylelint'], () => {
  return gulp.src([dir.src + '/_assets/scss/**/*.scss'])
    .pipe(plumber({errorHandler: notify.onError('<%= error.message %>')}))
    .pipe(sourcemaps.init())
    .pipe(sassGlob())
    .pipe(sass({
      importer: packageImporter({
        extensions: ['.scss', '.css']
      })
    }))
    // .pipe(replace(dir.replace, ''))
    .pipe(replace('/_assets/images', dir.wpImg))
    .pipe(postcss(cssProcessors))
    .pipe(cleanCSS(cleanConf))
    .pipe(sourcemaps.write('../sourcemaps', {addComment: DEBUG}))
    .pipe(gulp.dest(dir.dist + dir.wp + '/_assets/css'))
    .pipe(bs.stream());
});

gulp.task('stylus', ['clean:styles'], () => {
  return gulp.src([dir.src + '/_assets/styl/**/[^_]*.styl'], {nodir: true})
    .pipe(plumber({errorHandler: notify.onError('<%= error.message %>')}))
    .pipe(sourcemaps.init())
    .pipe(stylus({
      include: ['./node_modules'],
      'include css': true
    }))
    .pipe(postcss(cssProcessors))
    .pipe(cleanCSS(cleanConf))
    .pipe(sourcemaps.write('../sourcemaps', {addComment: DEBUG}))
    .pipe(gulp.dest(dir.dist + dir.wp + '/_assets/css'))
    .pipe(bs.stream());
});

gulp.task('scripts', () => {
  return gulp.src([dir.src + '/_assets/js/index.js'])
    .pipe(plumber({errorHandler: notify.onError('<%= error.message %>')}))
    .pipe(webpackStream(require('./webpack.config'), webpack))
    .on('error', function handleError() {
      this.emit('end');
    })
    .pipe(gulp.dest(dir.dist + dir.wp + '/_assets/js'))
    .pipe(bs.stream());
});

gulp.task('imagemin', () => {
  return gulp.src([dir.src + '/_assets/images/**/*.*'])
    .pipe(plumber({errorHandler: notify.onError('<%= error.message %>')}))
    .pipe(cached('imagemin'))
    .pipe(imagemin([
      pngquant({
        quality: '65-80',
        speed: 1
      }),
      mozjpeg({quality: 95}),
      imagemin.svgo({plugins: [
        {'removeDimensions': false},
        {'removeViewBox': false},
      ]}),
      imagemin.gifsicle()
    ]))
    .pipe(imagemin())
    .pipe(gulp.dest(dir.dist + dir.wpImg))
    .pipe(gulp.dest('./web' + dir.wpImg))
    .pipe(bs.stream());
});

gulp.task('pug', ['clean:html'], () => {
  return gulp.src([
    dir.src + '/pug/**/[^_]*.pug',
    dir.src + '/pug/**/_tpl_*.pug'
  ])
    .pipe(plumber({errorHandler: notify.onError('<%= error.message %>')}))
    .pipe(pug({pretty: true}))
    // .pipe(replace(dir.replace, ''))
    .pipe(replace('/_assets/images', dir.wpImg))
    .pipe(replace('/_assets/css', dir.wp + '/_assets/css'))
    .pipe(replace('/_assets/js', dir.wp + '/_assets/js'))
    .pipe(gulp.dest(dir.dist))
    .pipe(bs.stream());
});

gulp.task('html:file-include', ['delete-empty'], () => {
  return gulp.src([
    dir.src + '/**/[^_]*.html',
    dir.src + '/**/_tpl_*.html'
  ])
    .pipe(plumber({errorHandler: notify.onError('<%= error.message %>')}))
    .pipe(fileInclude({
      basepath: '@root'
    }))
    // .pipe(replace(dir.replace, ''))
    .pipe(replace('/_assets/images', dir.wpImg))
    .pipe(replace('/_assets/css', dir.wp + '/_assets/css'))
    .pipe(replace('/_assets/js', dir.wp + '/_assets/js'))
    .pipe(gulp.dest(dir.dist))
    .pipe(bs.stream());
});

gulp.task('html', callback => {
  runSequence('clean:html', 'html:file-include', 'pug', callback);
});

gulp.task('htmlmin', () => {
  return gulp.src([dir.dist + '/**/[^_tpl_]*.html'])
    .pipe(htmlmin({
      collapseWhitespace: true,
      removeComments: true
    }))
    .pipe(gulp.dest(dir.dist));
});

gulp.task('php', ['clean:php'], () => {
  return gulp.src([
    dir.src + '/**/[^_]*.php'
  ])
    .pipe(plumber({errorHandler: notify.onError('<%= error.message %>')}))
    // .pipe(cached('php'))
    // .pipe(replace(dir.replace, ''))
    .pipe(replace('/_assets/images', dir.wpImg))
    .pipe(replace('/_assets/css', dir.wp + '/_assets/css'))
    .pipe(replace('/_assets/js', dir.wp + '/_assets/js'))
    .pipe(gulp.dest(dir.dist))
    .pipe(bs.stream());
});

gulp.task('rev', () => {
  return gulp.src([dir.dist + dir.wp + '/_assets/**/*.+(css|js)'])
    .pipe(revAll.revision({
      transformFilename: (file, hash) => {
        const ext = path.extname(file.path);
        return path.basename(file.path, ext) + '-' + hash.substr(0, 20) + ext;
      }
    }))
    .pipe(revDel())
    .pipe(gulp.dest(dir.dist + dir.wp + '/_assets'))
    .pipe(revAll.manifestFile())
    .pipe(gulp.dest(dir.dist + dir.wp + '/_assets'));
});

gulp.task('revreplace', ['rev'], () => {
  const manifest = gulp.src([dir.dist + dir.wp + '/_assets/rev-manifest.json']);
  return gulp.src([dir.dist + '/**/*.html'])
    .pipe(revReplace({manifest: manifest}))
    .pipe(gulp.dest(dir.dist));
});

gulp.task('revision', ['revreplace']);

gulp.task('watch:styles', () => {
  return watch([dir.src + '/_assets/scss/**/*.scss'], () => {
    return gulp.start(['styles']);
  });
});

gulp.task('watch:stylus', () => {
  return watch([dir.src + '/_assets/styl/**/*.styl'], () => {
    return gulp.start(['stylus']);
  });
});

gulp.task('watch:scripts', () => {
  return watch([dir.src + '/_assets/js/**/*.js'], () => {
    return gulp.start(['scripts']);
  });
});

gulp.task('watch:images', () => {
  return watch([dir.src + '/_assets/images/**/*.*'], () => {
    return gulp.start(['imagemin']);
  });
});

gulp.task('watch:pug', () => {
  return watch([dir.src + '/**/*.pug'], () => {
    return gulp.start(['pug']);
  });
});

gulp.task('watch:html', () => {
  return watch([dir.src + '/**/*.html'], () => {
    return gulp.start(['html']);
  });
});

gulp.task('watch:php', () => {
  return watch([dir.src + '/**/*.php'], () => {
    return gulp.start(['php']);
  });
});

gulp.task('watch:src', () => {
  return watch([
    dir.src + '/**/*.*',
    '!' + dir.src + '/_assets/scss/**/*.scss',
    '!' + dir.src + '/_assets/styl/**/*.styl',
    '!' + dir.src + '/_assets/js/**/*.js',
    '!' + dir.src + '/_assets/images/**/*.*',
    '!' + dir.src + '/**/*.php',
    '!' + dir.src + '/**/*.pug',
    '!' + dir.src + '/**/*.html'
  ], () => {
    return gulp.start(['update']);
  });
});

gulp.task('watch', ['watch:styles', 'watch:scripts', 'watch:images', 'watch:html', 'watch:pug', 'watch:php', 'watch:src']);

/**
 * Build Tasks
 */
gulp.task('default', callback => {
  runSequence(
    ['clean:images', 'clean:scripts'],
    'copy',
    ['styles', 'scripts', 'imagemin', 'pug', 'php'],
    'browser-sync',
    'watch',
    callback);
});

gulp.task('build', callback => {
  runSequence(
    ['clean:images', 'clean:scripts'],
    'copy',
    ['styles', 'scripts', 'imagemin', 'pug', 'php'],
    // ['htmlmin', 'revision'],
    callback);
});
