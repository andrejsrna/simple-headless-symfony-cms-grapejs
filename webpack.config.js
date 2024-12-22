const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .enableStimulusBridge('./assets/controllers.json')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .enablePostCssLoader()
;

// Get the full Webpack config
const fullConfig = Encore.getWebpackConfig();

// Add a specific rule for node_modules CSS
fullConfig.module.rules.unshift({
    test: /\.css$/,
    include: /node_modules/,
    use: ['style-loader', 'css-loader']
});

// Safely configure Babel loader if it exists
const babelLoader = fullConfig.module.rules.find(
    rule => rule.loader === 'babel-loader'
);
if (babelLoader) {
    babelLoader.options = {
        ...babelLoader.options,
        sourceType: 'unambiguous'
    };
}

module.exports = fullConfig; 