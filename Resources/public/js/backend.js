/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./Resources/assets/js/backend.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./Resources/assets/js/backend.js":
/*!****************************************!*\
  !*** ./Resources/assets/js/backend.js ***!
  \****************************************/
/*! exports provided: onLoad */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"onLoad\", function() { return onLoad; });\n/* harmony import */ var _modules_emsReceiver__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/emsReceiver */ \"./Resources/assets/js/modules/emsReceiver.js\");\n\ndocument.addEventListener('DOMContentLoaded', onLoad);\nfunction onLoad() {\n  var metaId = document.head.querySelector('meta[property=\"id\"]');\n  var metaDomains = document.head.querySelector('meta[property=\"domains\"]');\n  new _modules_emsReceiver__WEBPACK_IMPORTED_MODULE_0__[\"default\"]({\n    'id': metaId.dataset.id,\n    'domains': JSON.parse(metaDomains.dataset.list)\n  });\n}\n\n//# sourceURL=webpack:///./Resources/assets/js/backend.js?");

/***/ }),

/***/ "./Resources/assets/js/modules/emsReceiver.js":
/*!****************************************************!*\
  !*** ./Resources/assets/js/modules/emsReceiver.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\nfunction _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\nvar DEFAULT_CONFIG = {\n  \"id\": false,\n  \"domains\": []\n};\n\nvar emsReceiver =\n/*#__PURE__*/\nfunction () {\n  function emsReceiver(options) {\n    var _this = this;\n\n    _classCallCheck(this, emsReceiver);\n\n    var config = Object.assign({}, DEFAULT_CONFIG, options);\n    this.domains = config.domains;\n    this.id = config.id;\n    this.lang = document.documentElement.lang;\n    this.basePath = window.location.pathname.replace(/\\/iframe\\/.*/g, '');\n\n    if (this.id !== false) {\n      window.addEventListener(\"message\", function (evt) {\n        return _this.onMessage(evt);\n      });\n    }\n  }\n\n  _createClass(emsReceiver, [{\n    key: \"onMessage\",\n    value: function onMessage(message) {\n      if (!this.domains.includes(message.origin)) {\n        return;\n      }\n\n      var data = emsReceiver.jsonParse(message.data);\n\n      if (!data) {\n        return;\n      }\n\n      var xhr = new XMLHttpRequest();\n      xhr.addEventListener(\"load\", function (evt) {\n        return emsReceiver.onResponse(evt, xhr, message);\n      });\n\n      switch (data.instruction) {\n        case \"form\":\n          {\n            xhr.open(\"GET\", this.basePath + \"/form/\" + this.id + '/' + this.lang);\n            xhr.setRequestHeader(\"Content-Type\", \"application/json\");\n            xhr.send();\n            break;\n          }\n\n        case \"submit\":\n          {\n            var urlEncoded = [];\n\n            for (var key in data.form) {\n              urlEncoded.push(encodeURI(key.concat('=').concat(data.form[key])));\n            }\n\n            xhr.open(\"POST\", this.basePath + \"/form/\" + this.id + \"/\" + this.lang);\n            xhr.setRequestHeader(\"Content-Type\", \"application/x-www-form-urlencoded\");\n\n            if ('token' in data) {\n              var token = data.token;\n              xhr.setRequestHeader('x-hashcash', [token.hash, token.nonce, token.data].join('|'));\n            }\n\n            xhr.send(urlEncoded.join('&'));\n            break;\n          }\n\n        default:\n          return;\n      }\n    }\n  }], [{\n    key: \"jsonParse\",\n    value: function jsonParse(string) {\n      try {\n        return JSON.parse(string);\n      } catch (e) {\n        return false;\n      }\n    }\n  }, {\n    key: \"onResponse\",\n    value: function onResponse(evt, xhr, message) {\n      if (xhr.status === 200) {\n        message.source.postMessage(xhr.responseText, message.origin);\n      }\n    }\n  }]);\n\n  return emsReceiver;\n}();\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (emsReceiver);\n\n//# sourceURL=webpack:///./Resources/assets/js/modules/emsReceiver.js?");

/***/ })

/******/ });