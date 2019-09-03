const path          = require('path')
const mix           = require('laravel-mix')
const webpack       = require('webpack')
const { version }   = require('./package.json')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
   .js('resources/js/admin/main.js', 'public/js/admin.js')
   .sass('resources/sass/app.scss', 'public/css')
   .options({
      postCss: [
          require('postcss-css-variables')()
      ]
   })

mix.webpackConfig({
  devServer: { disableHostCheck: true },
  resolve  : {
    alias: {
      '@'         : path.resolve(__dirname, 'resources/js/admin/'),
      'static'    : path.resolve(__dirname, 'resources/static/'),
      'validators': 'vuelidate/lib/validators',
    },
  },
  plugins: [
    new webpack.DefinePlugin({ __VERSION: JSON.stringify(version) }),
    new webpack.optimize.LimitChunkCountPlugin({
      maxChunks: 1,
    }),
  ],
})

if (mix.inProduction())
  mix.version()
else
  mix.sourceMaps()

