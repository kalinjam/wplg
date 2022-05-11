// Require path.
const path = require( 'path' );

// Configuration object.
const config = {
	module: {
		rules: [
			{
				// Look for any .js files.
				test: /\.js$/,
				// Exclude the node_modules folder.
				exclude: /node_modules/,
				// Use babel loader to transpile the JS files.
				loader: 'babel-loader'
			}
		]
	}
}

var configAdmin = Object.assign({}, config, {
    name: "configAdmin",
    entry: ['babel-polyfill', "./admin/js/wplg-admin.js"],
    output: {
        path: path.resolve( __dirname, './admin/build/js' ),
        publicPath: "/",
        filename: "wplg-admin.min.js"
    },
});

var configPublic = Object.assign({}, config, {
    name: "configPublic",
    entry: ['babel-polyfill', "./public/js/wplg-public.js"],
    output: {
        path: path.resolve( __dirname, './public/build/js' ),
        publicPath: "/",
        filename: "wplg-public.min.js"
    },
});



// Export the config object.
module.exports = [configAdmin, configPublic];