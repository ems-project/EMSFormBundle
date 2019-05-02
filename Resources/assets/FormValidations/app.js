'use strict';

/**
 * 1. In your webpack.config.js, create an alias with : 
 * 	  config.resolve.alias.emsf = path.resolve(__dirname, 'PATH_TO_THIS_FILE');
 * 2. In your app.js add  : import {} from 'emsf';
 * 3. Create the minified files with yarn or webpack
 */
import setNissInszValidation from './js/nissValidation';