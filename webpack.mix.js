require('dotenv').config();
const mix = require('laravel-mix');
const { exec } = require('child_process');
const path = require('path');
const webpack = require('webpack');

require('laravel-mix-polyfill');

mix.extend('ziggy', new class {
    register(config = {})
    {
        this.watch = config.watch ?? ['routes/**/*.php'];
        this.path = config.path ?? '';
        this.enabled = config.enabled ?? !Mix.inProduction();
    }
    boot()
    {
        if ( !this.enabled )
        {
            return;
        }
        const command = () => exec(`${process.env.APP_PHP_PATH} artisan ziggy:generate ${this.path}`, (error, stdout, stderr) => {
            if( error )
            {
                console.log('error: ', error);
            }
            
            if( stdout )
            {
                console.log(stdout);
            }
            
            if( stderr )
            {
                console.log('stderr: ', stderr);
            }
        });
       
        command();
       
        if( Mix.isWatching() && this.watch )
        {
            ((require('chokidar')).watch(this.watch)).on('change', (path) => {
                console.log(`${path} changed...`);
                command();
            });
        };
    }
}());

mix
    .ziggy({
        enabled: true,
        path: 'resources/js/ziggy.js'
    })
    .webpackConfig({
        stats: {
            children: false
        },
        resolve: {
            alias: {
                ziggy: path.resolve('vendor/tightenco/ziggy/dist'),
            },
        },
        // module: {
        //     rules: [{
        //         test: /\.jsx?$/,
        //         exclude: /(bower_components)/,
        //         use: [
        //             {
        //                 loader: 'babel-loader',
        //                 // options: (Config || mix.config).babel(),
        //                 options: {
        //                     presets: [
        //                         '@babel/preset-env'
        //                     ],
        //                     plugins: [
        //                         '@babel/plugin-syntax-throw-expressions',
        //                         '@babel/plugin-transform-runtime',
        //                     ]
        //                 }
        //             },
        //         ],
        //     }],
        // }
    });

mix
    .alias({
        // ziggy: path.resolve('vendor/tightenco/ziggy/dist/vue'), // or 'vendor/tightenco/ziggy/dist/vue' if you're using the Vue plugin
        ziggy: path.resolve('vendor/tightenco/ziggy/dist'), // or 'vendor/tightenco/ziggy/dist/vue' if you're using the Vue plugin
    })
    .options({
        processCssUrls: false,
        runtimeChunkPath: 'storage',
        terser: {
            extractComments: false,
        },
        autoprefixer: {
            enabled: true,
            options: {
                remove: false
            }
        },
        postCss:[
            require('autoprefixer')(),
        ],
    }).disableSuccessNotifications();

mix
    .js('resources/js/app.js', 'public/storage/js/app.min.js')
    .polyfill({
        enabled: true,
        useBuiltIns: 'usage',
        entryPoints: 'all',
        corejs: 3,
        targets: '> 0.25%, not dead, IE >= 8',
        debug: false,
    })
    .vue()
    .sass('resources/sass/error.scss', 'public/storage/css/error.min.css')
    .sass('resources/sass/app.scss', 'public/storage/css/app.min.css')
    .version();


if( !mix.inProduction() && process.env.MIX_USE_BROWSERSYNC == 'true' )
{
    // Обновление браузера после внесенных изменений
    mix
        .browserSync({
            proxy: process.env.APP_DOMAIN,
            https: {
                key: process.env.MIX_SSL_KEY,
                cert: process.env.MIX_SSL_CERT
            }
        });
}