/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@babel/runtime/regenerator/index.js":
/*!**********************************************************!*\
  !*** ./node_modules/@babel/runtime/regenerator/index.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

module.exports = __webpack_require__(/*! regenerator-runtime */ "./node_modules/regenerator-runtime/runtime.js");


/***/ }),

/***/ "./node_modules/regenerator-runtime/runtime.js":
/*!*****************************************************!*\
  !*** ./node_modules/regenerator-runtime/runtime.js ***!
  \*****************************************************/
/***/ ((module) => {

/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

var runtime = (function (exports) {
  "use strict";

  var Op = Object.prototype;
  var hasOwn = Op.hasOwnProperty;
  var undefined; // More compressible than void 0.
  var $Symbol = typeof Symbol === "function" ? Symbol : {};
  var iteratorSymbol = $Symbol.iterator || "@@iterator";
  var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
  var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

  function define(obj, key, value) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
    return obj[key];
  }
  try {
    // IE 8 has a broken Object.defineProperty that only works on DOM objects.
    define({}, "");
  } catch (err) {
    define = function(obj, key, value) {
      return obj[key] = value;
    };
  }

  function wrap(innerFn, outerFn, self, tryLocsList) {
    // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.
    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;
    var generator = Object.create(protoGenerator.prototype);
    var context = new Context(tryLocsList || []);

    // The ._invoke method unifies the implementations of the .next,
    // .throw, and .return methods.
    generator._invoke = makeInvokeMethod(innerFn, self, context);

    return generator;
  }
  exports.wrap = wrap;

  // Try/catch helper to minimize deoptimizations. Returns a completion
  // record like context.tryEntries[i].completion. This interface could
  // have been (and was previously) designed to take a closure to be
  // invoked without arguments, but in all the cases we care about we
  // already have an existing method we want to call, so there's no need
  // to create a new function object. We can even get away with assuming
  // the method takes exactly one argument, since that happens to be true
  // in every case, so we don't have to touch the arguments object. The
  // only additional allocation required is the completion record, which
  // has a stable shape and so hopefully should be cheap to allocate.
  function tryCatch(fn, obj, arg) {
    try {
      return { type: "normal", arg: fn.call(obj, arg) };
    } catch (err) {
      return { type: "throw", arg: err };
    }
  }

  var GenStateSuspendedStart = "suspendedStart";
  var GenStateSuspendedYield = "suspendedYield";
  var GenStateExecuting = "executing";
  var GenStateCompleted = "completed";

  // Returning this object from the innerFn has the same effect as
  // breaking out of the dispatch switch statement.
  var ContinueSentinel = {};

  // Dummy constructor functions that we use as the .constructor and
  // .constructor.prototype properties for functions that return Generator
  // objects. For full spec compliance, you may wish to configure your
  // minifier not to mangle the names of these two functions.
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}

  // This is a polyfill for %IteratorPrototype% for environments that
  // don't natively support it.
  var IteratorPrototype = {};
  define(IteratorPrototype, iteratorSymbol, function () {
    return this;
  });

  var getProto = Object.getPrototypeOf;
  var NativeIteratorPrototype = getProto && getProto(getProto(values([])));
  if (NativeIteratorPrototype &&
      NativeIteratorPrototype !== Op &&
      hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) {
    // This environment has a native %IteratorPrototype%; use it instead
    // of the polyfill.
    IteratorPrototype = NativeIteratorPrototype;
  }

  var Gp = GeneratorFunctionPrototype.prototype =
    Generator.prototype = Object.create(IteratorPrototype);
  GeneratorFunction.prototype = GeneratorFunctionPrototype;
  define(Gp, "constructor", GeneratorFunctionPrototype);
  define(GeneratorFunctionPrototype, "constructor", GeneratorFunction);
  GeneratorFunction.displayName = define(
    GeneratorFunctionPrototype,
    toStringTagSymbol,
    "GeneratorFunction"
  );

  // Helper for defining the .next, .throw, and .return methods of the
  // Iterator interface in terms of a single ._invoke method.
  function defineIteratorMethods(prototype) {
    ["next", "throw", "return"].forEach(function(method) {
      define(prototype, method, function(arg) {
        return this._invoke(method, arg);
      });
    });
  }

  exports.isGeneratorFunction = function(genFun) {
    var ctor = typeof genFun === "function" && genFun.constructor;
    return ctor
      ? ctor === GeneratorFunction ||
        // For the native GeneratorFunction constructor, the best we can
        // do is to check its .name property.
        (ctor.displayName || ctor.name) === "GeneratorFunction"
      : false;
  };

  exports.mark = function(genFun) {
    if (Object.setPrototypeOf) {
      Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);
    } else {
      genFun.__proto__ = GeneratorFunctionPrototype;
      define(genFun, toStringTagSymbol, "GeneratorFunction");
    }
    genFun.prototype = Object.create(Gp);
    return genFun;
  };

  // Within the body of any async function, `await x` is transformed to
  // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test
  // `hasOwn.call(value, "__await")` to determine if the yielded value is
  // meant to be awaited.
  exports.awrap = function(arg) {
    return { __await: arg };
  };

  function AsyncIterator(generator, PromiseImpl) {
    function invoke(method, arg, resolve, reject) {
      var record = tryCatch(generator[method], generator, arg);
      if (record.type === "throw") {
        reject(record.arg);
      } else {
        var result = record.arg;
        var value = result.value;
        if (value &&
            typeof value === "object" &&
            hasOwn.call(value, "__await")) {
          return PromiseImpl.resolve(value.__await).then(function(value) {
            invoke("next", value, resolve, reject);
          }, function(err) {
            invoke("throw", err, resolve, reject);
          });
        }

        return PromiseImpl.resolve(value).then(function(unwrapped) {
          // When a yielded Promise is resolved, its final value becomes
          // the .value of the Promise<{value,done}> result for the
          // current iteration.
          result.value = unwrapped;
          resolve(result);
        }, function(error) {
          // If a rejected Promise was yielded, throw the rejection back
          // into the async generator function so it can be handled there.
          return invoke("throw", error, resolve, reject);
        });
      }
    }

    var previousPromise;

    function enqueue(method, arg) {
      function callInvokeWithMethodAndArg() {
        return new PromiseImpl(function(resolve, reject) {
          invoke(method, arg, resolve, reject);
        });
      }

      return previousPromise =
        // If enqueue has been called before, then we want to wait until
        // all previous Promises have been resolved before calling invoke,
        // so that results are always delivered in the correct order. If
        // enqueue has not been called before, then it is important to
        // call invoke immediately, without waiting on a callback to fire,
        // so that the async generator function has the opportunity to do
        // any necessary setup in a predictable way. This predictability
        // is why the Promise constructor synchronously invokes its
        // executor callback, and why async functions synchronously
        // execute code before the first await. Since we implement simple
        // async functions in terms of async generators, it is especially
        // important to get this right, even though it requires care.
        previousPromise ? previousPromise.then(
          callInvokeWithMethodAndArg,
          // Avoid propagating failures to Promises returned by later
          // invocations of the iterator.
          callInvokeWithMethodAndArg
        ) : callInvokeWithMethodAndArg();
    }

    // Define the unified helper method that is used to implement .next,
    // .throw, and .return (see defineIteratorMethods).
    this._invoke = enqueue;
  }

  defineIteratorMethods(AsyncIterator.prototype);
  define(AsyncIterator.prototype, asyncIteratorSymbol, function () {
    return this;
  });
  exports.AsyncIterator = AsyncIterator;

  // Note that simple async functions are implemented on top of
  // AsyncIterator objects; they just return a Promise for the value of
  // the final result produced by the iterator.
  exports.async = function(innerFn, outerFn, self, tryLocsList, PromiseImpl) {
    if (PromiseImpl === void 0) PromiseImpl = Promise;

    var iter = new AsyncIterator(
      wrap(innerFn, outerFn, self, tryLocsList),
      PromiseImpl
    );

    return exports.isGeneratorFunction(outerFn)
      ? iter // If outerFn is a generator, return the full iterator.
      : iter.next().then(function(result) {
          return result.done ? result.value : iter.next();
        });
  };

  function makeInvokeMethod(innerFn, self, context) {
    var state = GenStateSuspendedStart;

    return function invoke(method, arg) {
      if (state === GenStateExecuting) {
        throw new Error("Generator is already running");
      }

      if (state === GenStateCompleted) {
        if (method === "throw") {
          throw arg;
        }

        // Be forgiving, per 25.3.3.3.3 of the spec:
        // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume
        return doneResult();
      }

      context.method = method;
      context.arg = arg;

      while (true) {
        var delegate = context.delegate;
        if (delegate) {
          var delegateResult = maybeInvokeDelegate(delegate, context);
          if (delegateResult) {
            if (delegateResult === ContinueSentinel) continue;
            return delegateResult;
          }
        }

        if (context.method === "next") {
          // Setting context._sent for legacy support of Babel's
          // function.sent implementation.
          context.sent = context._sent = context.arg;

        } else if (context.method === "throw") {
          if (state === GenStateSuspendedStart) {
            state = GenStateCompleted;
            throw context.arg;
          }

          context.dispatchException(context.arg);

        } else if (context.method === "return") {
          context.abrupt("return", context.arg);
        }

        state = GenStateExecuting;

        var record = tryCatch(innerFn, self, context);
        if (record.type === "normal") {
          // If an exception is thrown from innerFn, we leave state ===
          // GenStateExecuting and loop back for another invocation.
          state = context.done
            ? GenStateCompleted
            : GenStateSuspendedYield;

          if (record.arg === ContinueSentinel) {
            continue;
          }

          return {
            value: record.arg,
            done: context.done
          };

        } else if (record.type === "throw") {
          state = GenStateCompleted;
          // Dispatch the exception by looping back around to the
          // context.dispatchException(context.arg) call above.
          context.method = "throw";
          context.arg = record.arg;
        }
      }
    };
  }

  // Call delegate.iterator[context.method](context.arg) and handle the
  // result, either by returning a { value, done } result from the
  // delegate iterator, or by modifying context.method and context.arg,
  // setting context.delegate to null, and returning the ContinueSentinel.
  function maybeInvokeDelegate(delegate, context) {
    var method = delegate.iterator[context.method];
    if (method === undefined) {
      // A .throw or .return when the delegate iterator has no .throw
      // method always terminates the yield* loop.
      context.delegate = null;

      if (context.method === "throw") {
        // Note: ["return"] must be used for ES3 parsing compatibility.
        if (delegate.iterator["return"]) {
          // If the delegate iterator has a return method, give it a
          // chance to clean up.
          context.method = "return";
          context.arg = undefined;
          maybeInvokeDelegate(delegate, context);

          if (context.method === "throw") {
            // If maybeInvokeDelegate(context) changed context.method from
            // "return" to "throw", let that override the TypeError below.
            return ContinueSentinel;
          }
        }

        context.method = "throw";
        context.arg = new TypeError(
          "The iterator does not provide a 'throw' method");
      }

      return ContinueSentinel;
    }

    var record = tryCatch(method, delegate.iterator, context.arg);

    if (record.type === "throw") {
      context.method = "throw";
      context.arg = record.arg;
      context.delegate = null;
      return ContinueSentinel;
    }

    var info = record.arg;

    if (! info) {
      context.method = "throw";
      context.arg = new TypeError("iterator result is not an object");
      context.delegate = null;
      return ContinueSentinel;
    }

    if (info.done) {
      // Assign the result of the finished delegate to the temporary
      // variable specified by delegate.resultName (see delegateYield).
      context[delegate.resultName] = info.value;

      // Resume execution at the desired location (see delegateYield).
      context.next = delegate.nextLoc;

      // If context.method was "throw" but the delegate handled the
      // exception, let the outer generator proceed normally. If
      // context.method was "next", forget context.arg since it has been
      // "consumed" by the delegate iterator. If context.method was
      // "return", allow the original .return call to continue in the
      // outer generator.
      if (context.method !== "return") {
        context.method = "next";
        context.arg = undefined;
      }

    } else {
      // Re-yield the result returned by the delegate method.
      return info;
    }

    // The delegate iterator is finished, so forget it and continue with
    // the outer generator.
    context.delegate = null;
    return ContinueSentinel;
  }

  // Define Generator.prototype.{next,throw,return} in terms of the
  // unified ._invoke helper method.
  defineIteratorMethods(Gp);

  define(Gp, toStringTagSymbol, "Generator");

  // A Generator should always return itself as the iterator object when the
  // @@iterator function is called on it. Some browsers' implementations of the
  // iterator prototype chain incorrectly implement this, causing the Generator
  // object to not be returned from this call. This ensures that doesn't happen.
  // See https://github.com/facebook/regenerator/issues/274 for more details.
  define(Gp, iteratorSymbol, function() {
    return this;
  });

  define(Gp, "toString", function() {
    return "[object Generator]";
  });

  function pushTryEntry(locs) {
    var entry = { tryLoc: locs[0] };

    if (1 in locs) {
      entry.catchLoc = locs[1];
    }

    if (2 in locs) {
      entry.finallyLoc = locs[2];
      entry.afterLoc = locs[3];
    }

    this.tryEntries.push(entry);
  }

  function resetTryEntry(entry) {
    var record = entry.completion || {};
    record.type = "normal";
    delete record.arg;
    entry.completion = record;
  }

  function Context(tryLocsList) {
    // The root entry object (effectively a try statement without a catch
    // or a finally block) gives us a place to store values thrown from
    // locations where there is no enclosing try statement.
    this.tryEntries = [{ tryLoc: "root" }];
    tryLocsList.forEach(pushTryEntry, this);
    this.reset(true);
  }

  exports.keys = function(object) {
    var keys = [];
    for (var key in object) {
      keys.push(key);
    }
    keys.reverse();

    // Rather than returning an object with a next method, we keep
    // things simple and return the next function itself.
    return function next() {
      while (keys.length) {
        var key = keys.pop();
        if (key in object) {
          next.value = key;
          next.done = false;
          return next;
        }
      }

      // To avoid creating an additional object, we just hang the .value
      // and .done properties off the next function object itself. This
      // also ensures that the minifier will not anonymize the function.
      next.done = true;
      return next;
    };
  };

  function values(iterable) {
    if (iterable) {
      var iteratorMethod = iterable[iteratorSymbol];
      if (iteratorMethod) {
        return iteratorMethod.call(iterable);
      }

      if (typeof iterable.next === "function") {
        return iterable;
      }

      if (!isNaN(iterable.length)) {
        var i = -1, next = function next() {
          while (++i < iterable.length) {
            if (hasOwn.call(iterable, i)) {
              next.value = iterable[i];
              next.done = false;
              return next;
            }
          }

          next.value = undefined;
          next.done = true;

          return next;
        };

        return next.next = next;
      }
    }

    // Return an iterator with no values.
    return { next: doneResult };
  }
  exports.values = values;

  function doneResult() {
    return { value: undefined, done: true };
  }

  Context.prototype = {
    constructor: Context,

    reset: function(skipTempReset) {
      this.prev = 0;
      this.next = 0;
      // Resetting context._sent for legacy support of Babel's
      // function.sent implementation.
      this.sent = this._sent = undefined;
      this.done = false;
      this.delegate = null;

      this.method = "next";
      this.arg = undefined;

      this.tryEntries.forEach(resetTryEntry);

      if (!skipTempReset) {
        for (var name in this) {
          // Not sure about the optimal order of these conditions:
          if (name.charAt(0) === "t" &&
              hasOwn.call(this, name) &&
              !isNaN(+name.slice(1))) {
            this[name] = undefined;
          }
        }
      }
    },

    stop: function() {
      this.done = true;

      var rootEntry = this.tryEntries[0];
      var rootRecord = rootEntry.completion;
      if (rootRecord.type === "throw") {
        throw rootRecord.arg;
      }

      return this.rval;
    },

    dispatchException: function(exception) {
      if (this.done) {
        throw exception;
      }

      var context = this;
      function handle(loc, caught) {
        record.type = "throw";
        record.arg = exception;
        context.next = loc;

        if (caught) {
          // If the dispatched exception was caught by a catch block,
          // then let that catch block handle the exception normally.
          context.method = "next";
          context.arg = undefined;
        }

        return !! caught;
      }

      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        var record = entry.completion;

        if (entry.tryLoc === "root") {
          // Exception thrown outside of any try block that could handle
          // it, so set the completion value of the entire function to
          // throw the exception.
          return handle("end");
        }

        if (entry.tryLoc <= this.prev) {
          var hasCatch = hasOwn.call(entry, "catchLoc");
          var hasFinally = hasOwn.call(entry, "finallyLoc");

          if (hasCatch && hasFinally) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            } else if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else if (hasCatch) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            }

          } else if (hasFinally) {
            if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else {
            throw new Error("try statement without catch or finally");
          }
        }
      }
    },

    abrupt: function(type, arg) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc <= this.prev &&
            hasOwn.call(entry, "finallyLoc") &&
            this.prev < entry.finallyLoc) {
          var finallyEntry = entry;
          break;
        }
      }

      if (finallyEntry &&
          (type === "break" ||
           type === "continue") &&
          finallyEntry.tryLoc <= arg &&
          arg <= finallyEntry.finallyLoc) {
        // Ignore the finally entry if control is not jumping to a
        // location outside the try/catch block.
        finallyEntry = null;
      }

      var record = finallyEntry ? finallyEntry.completion : {};
      record.type = type;
      record.arg = arg;

      if (finallyEntry) {
        this.method = "next";
        this.next = finallyEntry.finallyLoc;
        return ContinueSentinel;
      }

      return this.complete(record);
    },

    complete: function(record, afterLoc) {
      if (record.type === "throw") {
        throw record.arg;
      }

      if (record.type === "break" ||
          record.type === "continue") {
        this.next = record.arg;
      } else if (record.type === "return") {
        this.rval = this.arg = record.arg;
        this.method = "return";
        this.next = "end";
      } else if (record.type === "normal" && afterLoc) {
        this.next = afterLoc;
      }

      return ContinueSentinel;
    },

    finish: function(finallyLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.finallyLoc === finallyLoc) {
          this.complete(entry.completion, entry.afterLoc);
          resetTryEntry(entry);
          return ContinueSentinel;
        }
      }
    },

    "catch": function(tryLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc === tryLoc) {
          var record = entry.completion;
          if (record.type === "throw") {
            var thrown = record.arg;
            resetTryEntry(entry);
          }
          return thrown;
        }
      }

      // The context.catch method must only be called with a location
      // argument that corresponds to a known catch block.
      throw new Error("illegal catch attempt");
    },

    delegateYield: function(iterable, resultName, nextLoc) {
      this.delegate = {
        iterator: values(iterable),
        resultName: resultName,
        nextLoc: nextLoc
      };

      if (this.method === "next") {
        // Deliberately forget the last sent value so that we don't
        // accidentally pass it on to the delegate.
        this.arg = undefined;
      }

      return ContinueSentinel;
    }
  };

  // Regardless of whether this script is executing as a CommonJS module
  // or not, return the runtime object so that we can declare the variable
  // regeneratorRuntime in the outer scope, which allows this module to be
  // injected easily by `bin/regenerator --include-runtime script.js`.
  return exports;

}(
  // If this script is executing as a CommonJS module, use module.exports
  // as the regeneratorRuntime namespace. Otherwise create a new empty
  // object. Either way, the resulting object will be used to initialize
  // the regeneratorRuntime variable at the top of this file.
   true ? module.exports : 0
));

try {
  regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
  // This module should not be running in strict mode, so the above
  // assignment should always work unless something is misconfigured. Just
  // in case runtime.js accidentally runs in strict mode, in modern engines
  // we can explicitly access globalThis. In older engines we can escape
  // strict mode using a global Function call. This could conceivably fail
  // if a Content Security Policy forbids using Function, but in that case
  // the proper solution is to fix the accidental strict mode problem. If
  // you've misconfigured your bundler to force strict mode and applied a
  // CSP to forbid Function, and you're not willing to fix either of those
  // problems, please detail your unique predicament in a GitHub issue.
  if (typeof globalThis === "object") {
    globalThis.regeneratorRuntime = runtime;
  } else {
    Function("r", "regeneratorRuntime = r")(runtime);
  }
}


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!****************************************!*\
  !*** ./resources/js/communications.js ***!
  \****************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

/**
 * Communications module for handling messaging functionality
 */

/**
 * Load conversation content when a user is clicked
 * @param {string} url - The URL to fetch conversation data
 * @param {Event} event - Optional event object for click handlers
 */
window.loadConversation = function (url) {
  var event = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var messageContainer = document.getElementById('message-container'); // Show loading state

  messageContainer.innerHTML = "\n        <div class=\"flex h-full w-full items-center justify-center\">\n            <svg class=\"animate-spin h-10 w-10 text-indigo-500\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\">\n                <circle class=\"opacity-25\" cx=\"12\" cy=\"12\" r=\"10\" stroke=\"currentColor\" stroke-width=\"4\"></circle>\n                <path class=\"opacity-75\" fill=\"currentColor\" d=\"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\"></path>\n            </svg>\n        </div>\n    "; // Only update conversation highlighting if this was triggered by a click event

  if (event && event.currentTarget) {
    // Highlight selected conversation
    var conversations = document.querySelectorAll('.w-72 a');
    conversations.forEach(function (conv) {
      conv.classList.remove('bg-indigo-50');
    }); // Add highlight to clicked conversation

    event.currentTarget.classList.add('bg-indigo-50');
  } // Fetch conversation content


  fetch(url, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  }).then(function (response) {
    return response.text();
  }).then(function (html) {
    messageContainer.innerHTML = html; // Initialize event handlers for the loaded content

    initializeMessageContentHandlers(); // Extract conversation ID from URL and set it in threadManagement

    var pathParts = url.split('/');
    var conversationIndex = pathParts.findIndex(function (part) {
      return part === 'conversation';
    });

    if (conversationIndex !== -1 && pathParts[conversationIndex + 1]) {
      window.threadManagement.activeConversationId = pathParts[conversationIndex + 1];
    } // Update URL without page reload only if this was a navigation action


    if (event && event.currentTarget) {
      window.history.pushState({}, '', url);
    }
  })["catch"](function (error) {
    console.error('Error loading conversation:', error);
    messageContainer.innerHTML = "\n            <div class=\"flex h-full w-full items-center justify-center flex-col\">\n                <svg class=\"w-16 h-16 text-red-500 mb-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"></path>\n                </svg>\n                <h2 class=\"text-xl text-gray-700\">Error loading conversation</h2>\n                <p class=\"text-gray-500 mt-2\">Please try again later</p>\n            </div>\n        ";
  });
};
/**
 * Initialize event handlers for message content
 */


function initializeMessageContentHandlers() {
  // Initialize thread form submission
  var form = document.getElementById('newThreadForm');

  if (form) {
    // Remove any existing listener first
    form.removeEventListener('submit', handleThreadFormSubmit);
    form.addEventListener('submit', handleThreadFormSubmit);
  } // Note: Message form uses inline onsubmit handler, so we don't add event listener here
  // to avoid duplicate submissions
  // Ensure thread dropdown is hidden when content is loaded


  var threadMenuOptions = document.getElementById('thread-menu-options');

  if (threadMenuOptions) {
    threadMenuOptions.classList.add('hidden');
  } // Scroll message list to bottom


  scrollToBottom();
}
/**
 * Handle thread form submission
 * @param {Event} e - The form submit event
 */


function handleThreadFormSubmit(e) {
  e.preventDefault();
  var form = this;
  var formData = new FormData(form);
  var token = document.querySelector('meta[name="csrf-token"]').content;
  fetch(form.action, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': token,
      'X-Requested-With': 'XMLHttpRequest'
    }
  }).then(function (response) {
    return response.json();
  }).then(function (data) {
    if (data.success) {
      // Reload the page with the new thread
      window.location.href = "".concat(form.action.replace('/thread', ''), "?thread_id=").concat(data.thread_id);
    } else {
      console.error('Error creating thread');
      alert('Error creating thread');
    }
  })["catch"](function (error) {
    console.error('Error:', error);
    alert('An error occurred while creating the thread');
  });
} // Note: Message form submission is handled by window.handleMessageSubmit (inline handler)
// This function was removed to avoid duplicate submissions

/**
 * Thread Management Functions
 * Handles all conversation thread interactions using Laravel Echo
 */


window.threadManagement = {
  activeConversationId: null,
  currentThreadId: null,
  currentEchoChannels: {
    // Keep track of Echo channels
    conversation: null,
    thread: null
  },
  // Initialize thread management
  init: function init() {
    // Clean up any existing listeners
    this.removeDropdownListener(); // Set up any initial state or event listeners

    this.getActiveConversationIdFromUrl();
    this.setupEventListeners(); // If an initial conversation is active, subscribe via Echo

    if (this.activeConversationId) {
      this.subscribeToChannels(this.activeConversationId, this.currentThreadId);
    }
  },
  setupEventListeners: function setupEventListeners() {
    var _this = this;

    // Set up event listeners for conversation links if on the right page
    var conversationLinks = document.querySelectorAll('.user-conversation-link');

    if (conversationLinks && conversationLinks.length > 0) {
      conversationLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          var conversationId = link.dataset.conversationId;

          if (conversationId) {
            _this.loadConversation(conversationId);
          }
        });
      });
    } // Set up thread form submission handler


    var threadForm = document.getElementById('newThreadForm');

    if (threadForm) {
      threadForm.addEventListener('submit', function (e) {
        _this.createNewThread(e);
      });
    }
  },
  // Subscribe to channels using Laravel Echo
  subscribeToChannels: function subscribeToChannels(conversationId, threadId) {
    var _this2 = this;

    if (!window.Echo) {
      console.error('Laravel Echo not initialized.');
      return;
    }

    this.leaveChannels();
    var conversationChannelName = "conversation-".concat(conversationId);
    this.currentEchoChannels.conversation = window.Echo.channel(conversationChannelName) // Use .channel() for public
    .listen('.new.message', function (e) {
      console.log("Received message on ".concat(conversationChannelName, ":"), e);

      if (e && e.thread_id != _this2.currentThreadId) {
        _this2.showThreadNotification(e.thread_id);
      }
    }).error(function (error) {
      console.error("Error subscribing to ".concat(conversationChannelName, ":"), error);
    });
    console.log("Subscribed to Echo channel: ".concat(conversationChannelName)); // Subscribe to the PUBLIC specific thread channel if a threadId is provided

    if (threadId) {
      var threadChannelName = "thread-".concat(threadId);
      this.currentEchoChannels.thread = window.Echo.channel(threadChannelName) // Use .channel() for public
      .listen('.new.message', function (e) {
        console.log("Received message on ".concat(threadChannelName, ":"), e);

        if (e && e.thread_id == _this2.currentThreadId) {
          var existingMessage = document.querySelector("[data-message-id=\"".concat(e.id, "\"]"));

          if (!existingMessage) {
            _this2.appendNewMessage(e);
          }
        }
      }).error(function (error) {
        console.error("Error subscribing to ".concat(threadChannelName, ":"), error);
      });
      console.log("Subscribed to Echo channel: ".concat(threadChannelName));
      this.currentThreadId = threadId;
    }
  },
  // Leave current Echo channels
  leaveChannels: function leaveChannels() {
    if (this.currentEchoChannels.conversation) {
      window.Echo.leaveChannel(this.currentEchoChannels.conversation.name);
      console.log("Left Echo channel: ".concat(this.currentEchoChannels.conversation.name));
      this.currentEchoChannels.conversation = null;
    }

    if (this.currentEchoChannels.thread) {
      window.Echo.leaveChannel(this.currentEchoChannels.thread.name);
      console.log("Left Echo channel: ".concat(this.currentEchoChannels.thread.name));
      this.currentEchoChannels.thread = null;
    }

    this.currentThreadId = null;
  },
  // Show notification dot on a thread tab
  showThreadNotification: function showThreadNotification(threadId) {
    var threadTab = document.querySelector(".thread-tab[data-thread-id=\"".concat(threadId, "\"]"));

    if (threadTab) {
      threadTab.classList.add('relative');
      var existingDot = threadTab.querySelector('.notification-dot');

      if (existingDot) {
        existingDot.remove();
      }

      var notificationDot = document.createElement('span');
      notificationDot.className = 'notification-dot absolute -top-1 -right-1 bg-red-500 rounded-full w-3 h-3';
      threadTab.appendChild(notificationDot);
    }
  },
  // Append a new message to the message list
  appendNewMessage: function appendNewMessage(message) {
    console.log('Trying to append message:', message);
    var messageList = document.querySelector('.message-list .space-y-4');
    console.log('Message list element found:', messageList);

    if (!messageList) {
      console.error('Message list container not found. Trying alternative selectors...'); // Try different selectors if the original one fails

      var alternativeContainers = [document.querySelector('.message-list'), document.querySelector('#message-container .space-y-4'), document.querySelector('.messages-content .message-list .space-y-4')];

      for (var _i = 0, _alternativeContainer = alternativeContainers; _i < _alternativeContainer.length; _i++) {
        var container = _alternativeContainer[_i];

        if (container) {
          console.log('Found alternative container:', container);
          this.appendMessageToContainer(message, container);
          return;
        }
      }

      console.error('No suitable message container found. Cannot append message.');
      return;
    }

    this.appendMessageToContainer(message, messageList);
  },
  // Helper method to append message to a container
  appendMessageToContainer: function appendMessageToContainer(message, container) {
    var isFromBusiness = message.sender_type === 'App\\Models\\Business';
    var messageElement = document.createElement('div');
    messageElement.className = "flex ".concat(isFromBusiness ? 'justify-end' : 'justify-start');
    messageElement.setAttribute('data-message-id', message.id);
    var attachmentsHtml = '';

    if (message.attachments && message.attachments.length > 0) {
      var attachmentsContent = '';
      message.attachments.forEach(function (attachment) {
        var imagePreview = '';

        if (attachment.mime && attachment.mime.startsWith('image/')) {
          imagePreview = "<div class=\"mb-1\">\n            <img src=\"".concat(window.location.origin, "/storage/").concat(attachment.path, "\" alt=\"").concat(attachment.name || 'Image', "\" \n                class=\"max-h-48 rounded border\">\n          </div>");
        }

        attachmentsContent += "\n          <div class=\"mb-1\">\n            ".concat(imagePreview, "\n            <a href=\"").concat(window.location.origin, "/storage/").concat(attachment.path, "\" target=\"_blank\" \n               class=\"flex items-center text-xs text-blue-600 hover:underline\">\n              <svg class=\"w-4 h-4 mr-1\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" \n                     d=\"M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13\"></path>\n              </svg>\n              ").concat(attachment.name || 'Attachment', "\n              ").concat(attachment.size ? "<span class=\"text-gray-500 ml-1\">(".concat(Math.round(attachment.size / 1024), " KB)</span>") : '', "\n            </a>\n          </div>\n        ");
      });
      attachmentsHtml = "\n        <div class=\"mt-2 space-y-2 p-2 bg-white bg-opacity-50 rounded-md\">\n          ".concat(attachmentsContent, "\n        </div>\n      ");
    }

    var timeFormatted = this.formatMessageTime(message.created_at);
    messageElement.innerHTML = "\n      <div class=\"max-w-xs md:max-w-md lg:max-w-lg rounded-lg px-4 py-2 ".concat(isFromBusiness ? 'bg-indigo-100 text-gray-800' : 'bg-gray-100 text-gray-800', "\">\n        <p class=\"text-sm\">").concat(message.content, "</p>\n        \n        ").concat(attachmentsHtml, "\n        \n        <div class=\"mt-1 text-xs text-gray-500 text-right\">\n          ").concat(timeFormatted, "\n          ").concat(message.is_read && isFromBusiness ? '<span class="ml-1 text-green-600">✓</span>' : '', "\n        </div>\n      </div>\n    ");
    container.appendChild(messageElement);
    this.scrollToBottom();
    console.log('Message appended successfully');
  },
  // Format message time helper
  formatMessageTime: function formatMessageTime(timestamp) {
    if (!timestamp) return '';
    var date = new Date(timestamp);
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    var formattedHours = hours % 12 === 0 ? 12 : hours % 12;
    var formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
    return "".concat(formattedHours, ":").concat(formattedMinutes, " ").concat(ampm);
  },
  // Scroll to bottom of message list
  scrollToBottom: function scrollToBottom() {
    var messageList = document.querySelector('.message-list');

    if (messageList) {
      messageList.scrollTop = messageList.scrollHeight;
    }
  },
  // Get current conversation ID from URL or active element
  getActiveConversationIdFromUrl: function getActiveConversationIdFromUrl() {
    var pathParts = window.location.pathname.split('/');
    var conversationIndex = pathParts.indexOf('conversation');

    if (conversationIndex !== -1 && pathParts[conversationIndex + 1]) {
      this.activeConversationId = pathParts[conversationIndex + 1];
      return this.activeConversationId;
    }

    var activeLink = document.querySelector('.user-conversation-link.bg-indigo-50');

    if (activeLink && activeLink.dataset.conversationId) {
      this.activeConversationId = activeLink.dataset.conversationId;
      return this.activeConversationId;
    }

    return null;
  },
  // Show the new thread modal
  showNewThreadModal: function showNewThreadModal() {
    var conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();

    if (!conversationId) {
      alert('Please select a conversation first');
      return;
    }

    var modal = document.getElementById('newThreadModal');

    if (modal) {
      var form = modal.querySelector('form');

      if (form) {
        var _document$querySelect;

        var token = (_document$querySelect = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.content;

        if (token) {
          var csrfField = form.querySelector('input[name="_token"]');

          if (!csrfField) {
            csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = token;
            form.appendChild(csrfField);
          } else {
            csrfField.value = token;
          }
        }
      }

      modal.classList.remove('hidden');
    }
  },
  // Hide the new thread modal
  hideNewThreadModal: function hideNewThreadModal() {
    var modal = document.getElementById('newThreadModal');

    if (modal) {
      modal.classList.add('hidden');
    }
  },
  // Create a new thread
  createNewThread: function createNewThread(event) {
    var _this3 = this;

    event.preventDefault();
    var conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();

    if (!conversationId) {
      alert('Please select a conversation first');
      return;
    }

    var form = event.target;
    var formData = new FormData(form);
    var businessId = window.location.pathname.split('/')[2];
    var submitBtn = form.querySelector('button[type="submit"]');
    var originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Creating...';
    submitBtn.disabled = true;
    fetch("/business/".concat(businessId, "/communications/conversation/").concat(conversationId, "/thread"), {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('Network response was not ok: ' + response.statusText);
      }

      return response.json();
    }).then(function (data) {
      _this3.hideNewThreadModal();

      if (data.success) {
        console.log('Thread created successfully:', data);

        _this3.loadConversation(conversationId, data.thread_id);
      } else {
        console.error('Error creating thread:', data.message || 'Unknown error');
        alert('Error creating thread: ' + (data.message || 'Unknown error'));
      }
    })["catch"](function (error) {
      console.error('Error creating thread:', error);
      alert('An error occurred while creating the thread. Please try again.');
    })["finally"](function () {
      form.reset();
      submitBtn.innerHTML = originalBtnText;
      submitBtn.disabled = false;
    });
  },
  // Load a conversation
  loadConversation: function loadConversation(conversationId) {
    var _this4 = this;

    var threadId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

    if (!conversationId) {
      console.error('No conversation ID provided');
      return;
    } // Clean up any existing dropdown listeners


    this.removeDropdownListener();
    document.querySelectorAll('.user-conversation-link').forEach(function (link) {
      if (link.dataset.conversationId == conversationId) {
        link.classList.add('bg-indigo-50');
      } else {
        link.classList.remove('bg-indigo-50');
      }
    });
    this.activeConversationId = conversationId;
    var businessId = window.location.pathname.split('/')[2];
    var url = "/business/".concat(businessId, "/communications/conversation/").concat(conversationId, "?ajax=1");

    if (threadId) {
      url += "&thread_id=".concat(threadId);
    }

    var messageContainer = document.getElementById('message-container');

    if (messageContainer) {
      messageContainer.innerHTML = '<div class="flex h-full w-full items-center justify-center"><div class="loader"></div></div>';
      fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      }).then(function (response) {
        if (!response.ok) {
          throw new Error('Network response was not ok: ' + response.statusText);
        }

        return response.text();
      }).then(function (html) {
        messageContainer.innerHTML = html;
        var threadIdInput = document.querySelector('input[name="thread_id"]');
        var loadedThreadId = threadIdInput ? threadIdInput.value : null;

        _this4.subscribeToChannels(conversationId, loadedThreadId); // Update menu button visibility for the loaded thread


        if (loadedThreadId) {
          _this4.updateThreadMenuButtonVisibility(loadedThreadId);

          _this4.updateThreadMenuOptions(loadedThreadId); // Ensure dropdown is hidden after loading


          var threadMenuOptions = document.getElementById('thread-menu-options');

          if (threadMenuOptions) {
            threadMenuOptions.classList.add('hidden');
          }
        }

        _this4.scrollToBottom();
      })["catch"](function (error) {
        console.error('Error loading conversation:', error);
        messageContainer.innerHTML = "\n          <div class=\"flex flex-col p-3 overflow-y-auto fw-scrollbar flex-grow\">\n            <div class=\"flex h-full w-full items-center justify-center flex-col\">\n              <svg class=\"w-16 h-16 text-red-300 mb-4\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">\n                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\"/>\n              </svg>\n              <h2 class=\"text-xl text-red-500\">Error loading conversation</h2>\n              <p class=\"text-gray-400 mt-2\">Please try again or contact support</p>\n            </div>\n          </div>\n        ";
      });
    }
  },
  // Switch between threads
  switchThread: function switchThread(event, threadId) {
    var _this5 = this;

    event.preventDefault(); // Clean up any existing dropdown listeners

    this.removeDropdownListener();
    var conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();

    if (!conversationId) {
      console.error('No active conversation found. Please select a conversation first.');
      alert('Please select a conversation first before switching threads.');
      return;
    } // Update active thread styling


    document.querySelectorAll('.thread-tab').forEach(function (tab) {
      tab.classList.remove('bg-blue-600', 'text-white', 'active');
      tab.classList.add('bg-gray-200', 'text-gray-700');
    });
    var activeTab = document.querySelector(".thread-tab[data-thread-id=\"".concat(threadId, "\"]"));

    if (activeTab) {
      activeTab.classList.remove('bg-gray-200', 'text-gray-700');
      activeTab.classList.add('bg-blue-600', 'text-white', 'active');
    }

    this.currentThreadId = threadId;
    var activeThreadTab = document.querySelector(".thread-tab[data-thread-id=\"".concat(threadId, "\"]"));

    if (activeThreadTab) {
      var notificationDot = activeThreadTab.querySelector('.notification-dot');

      if (notificationDot) {
        notificationDot.remove();
      }
    }

    var businessId = window.location.pathname.split('/')[2];
    var url = "/business/".concat(businessId, "/communications/conversation/").concat(conversationId, "?thread_id=").concat(threadId, "&ajax=1");
    fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('Network response was not ok: ' + response.statusText);
      }

      return response.text();
    }).then(function (html) {
      var tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      var messageContainer = document.querySelector('.message-list');
      var newMessageList = tempDiv.querySelector('.message-list');

      if (messageContainer && newMessageList) {
        messageContainer.innerHTML = newMessageList.innerHTML;
      } // Update thread ID input for message form (only if not "all" thread)


      var threadIdInput = document.querySelector('input[name="thread_id"]');

      if (threadIdInput && threadId !== 'all') {
        threadIdInput.value = threadId;
      }

      _this5.updateThreadMenuOptions(threadId);

      _this5.updateThreadMenuButtonVisibility(threadId); // Ensure dropdown is hidden after thread switch


      var threadMenuOptions = document.getElementById('thread-menu-options');

      if (threadMenuOptions) {
        threadMenuOptions.classList.add('hidden');
      }

      _this5.scrollToBottom();
    })["catch"](function (error) {
      console.error('Error switching thread:', error);
      alert('Error loading thread messages. Please try again.');
    });
  },
  // Update thread menu button visibility based on thread type
  updateThreadMenuButtonVisibility: function updateThreadMenuButtonVisibility(threadId) {
    var activeThreadTab = document.querySelector(".thread-tab[data-thread-id=\"".concat(threadId, "\"]"));
    if (!activeThreadTab) return;
    var threadName = activeThreadTab.textContent.trim();
    var isNonDeletable = threadName.toLowerCase().includes('general') || threadName.toLowerCase().includes('all messages') || threadId === 'all';
    var menuButton = document.querySelector('.thread-menu-btn');

    if (menuButton) {
      if (isNonDeletable) {
        menuButton.style.display = 'none';
      } else {
        menuButton.style.display = 'inline-flex';
      }
    }
  },
  // Update thread menu options with current thread information
  updateThreadMenuOptions: function updateThreadMenuOptions(threadId) {
    var activeThreadTab = document.querySelector(".thread-tab[data-thread-id=\"".concat(threadId, "\"]"));
    if (!activeThreadTab) return;
    var threadName = activeThreadTab.textContent.trim();
    var conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();
    var threadMenuOptions = document.getElementById('thread-menu-options');

    if (threadMenuOptions) {
      var deleteLink = threadMenuOptions.querySelector('a');

      if (deleteLink) {
        // Check if this is a non-deletable thread (General or All Messages)
        var isNonDeletable = threadName.toLowerCase().includes('general') || threadName.toLowerCase().includes('all messages') || threadId === 'all';

        if (isNonDeletable) {
          // Hide the entire menu for non-deletable threads
          threadMenuOptions.style.display = 'none';
          threadMenuOptions.classList.add('hidden');
          return;
        } else {
          // Make the menu available for deletable threads (but keep it hidden until clicked)
          threadMenuOptions.style.display = ''; // Remove any inline display style

          threadMenuOptions.classList.add('hidden'); // Ensure it stays hidden until clicked
        }

        deleteLink.setAttribute('onclick', "window.parent.threadManagement.confirmDeleteThread(event, ".concat(conversationId, ", ").concat(threadId, ")"));
        var textSpan = deleteLink.querySelector('span');

        if (textSpan) {
          textSpan.innerHTML = "\n            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n              <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\" />\n            </svg>\n            Delete \"".concat(threadName, "\"\n          ");
        }
      }
    }
  },
  // Toggle thread dropdown menu before the + button
  toggleThreadMenu: function toggleThreadMenu(event) {
    event.preventDefault();
    event.stopPropagation();
    var activeThreadTab = document.querySelector('.thread-tab.bg-blue-600');
    if (!activeThreadTab) return;
    var threadId = activeThreadTab.dataset.threadId;
    var threadName = activeThreadTab.textContent.trim(); // Check if this thread is deletable

    var isNonDeletable = threadName.toLowerCase().includes('general') || threadName.toLowerCase().includes('all messages') || threadId === 'all';

    if (isNonDeletable) {
      // Don't show dropdown for non-deletable threads
      return;
    }

    var optionsMenu = document.getElementById('thread-menu-options');

    if (optionsMenu) {
      var isCurrentlyHidden = optionsMenu.classList.contains('hidden'); // Close any existing dropdown first

      if (!isCurrentlyHidden) {
        optionsMenu.classList.add('hidden');
        this.removeDropdownListener();
        return;
      } // Update menu options and show the dropdown only if thread is deletable


      this.updateThreadMenuOptions(threadId);
      optionsMenu.classList.remove('hidden');
      this.addDropdownListener();
    }
  },
  // Add dropdown close listener
  addDropdownListener: function addDropdownListener() {
    var _this6 = this;

    // Remove any existing listener first
    this.removeDropdownListener();

    this.dropdownCloseHandler = function (e) {
      if (!e.target.closest('#thread-menu-options, .thread-menu-btn')) {
        var menu = document.getElementById('thread-menu-options');

        if (menu) {
          menu.classList.add('hidden');
        }

        _this6.removeDropdownListener();
      }
    }; // Add listener with a small delay to prevent immediate closure


    setTimeout(function () {
      document.addEventListener('click', _this6.dropdownCloseHandler);
    }, 100);
  },
  // Remove dropdown close listener
  removeDropdownListener: function removeDropdownListener() {
    if (this.dropdownCloseHandler) {
      document.removeEventListener('click', this.dropdownCloseHandler);
      this.dropdownCloseHandler = null;
    }
  },
  // Confirm thread deletion
  confirmDeleteThread: function confirmDeleteThread(event, conversationId, threadId) {
    var _this7 = this;

    event.preventDefault();
    this.pendingDeleteConversationId = conversationId;
    this.pendingDeleteThreadId = threadId;
    var modal = document.getElementById('confirmDeleteModal');

    if (modal) {
      modal.classList.remove('hidden');

      document.getElementById('cancelDeleteBtn').onclick = function () {
        modal.classList.add('hidden');
        _this7.pendingDeleteConversationId = null;
        _this7.pendingDeleteThreadId = null;
      };

      document.getElementById('confirmDeleteBtn').onclick = function () {
        _this7.deleteThread();
      };
    }
  },
  // Delete a thread
  deleteThread: function deleteThread() {
    var _document$querySelect2,
        _this8 = this;

    if (!this.pendingDeleteConversationId || !this.pendingDeleteThreadId) {
      return;
    }

    var conversationId = this.pendingDeleteConversationId;
    var threadId = this.pendingDeleteThreadId;
    var businessId = window.location.pathname.split('/')[2];
    var token = (_document$querySelect2 = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect2 === void 0 ? void 0 : _document$querySelect2.content;
    var deleteBtn = document.getElementById('confirmDeleteBtn');
    var originalBtnText = deleteBtn.textContent;
    deleteBtn.textContent = 'Deleting...';
    deleteBtn.disabled = true;
    fetch("/business/".concat(businessId, "/communications/conversation/").concat(conversationId, "/thread/").concat(threadId), {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json'
      }
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('Network response was not ok: ' + response.statusText);
      }

      return response.json();
    }).then(function (data) {
      document.getElementById('confirmDeleteModal').classList.add('hidden');

      if (data.success) {
        if (data.is_only_thread) {
          window.location.href = "/business/".concat(businessId, "/communications");
        } else if (data.default_thread_id) {
          _this8.loadConversation(conversationId, data.default_thread_id);
        }
      } else {
        console.error('Error deleting thread:', data.message || 'Unknown error');
        alert('Error deleting thread: ' + (data.message || 'Unknown error'));
      }
    })["catch"](function (error) {
      console.error('Error deleting thread:', error);
      alert('An error occurred while deleting the thread. Please try again.');
    })["finally"](function () {
      deleteBtn.textContent = originalBtnText;
      deleteBtn.disabled = false;
      _this8.pendingDeleteConversationId = null;
      _this8.pendingDeleteThreadId = null;
    });
  }
}; // Handle message form submission - add data-message-id to new messages

window.handleMessageSubmit = /*#__PURE__*/function () {
  var _ref = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee(event) {
    var _document$querySelect3;

    var form, formData, submitButton, token, response, data, messageInput, fileInput, selectedFiles;
    return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            event.preventDefault();
            form = event.target;
            formData = new FormData(form);
            submitButton = form.querySelector('button[type="submit"]');
            token = (_document$querySelect3 = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect3 === void 0 ? void 0 : _document$querySelect3.content;
            submitButton.disabled = true;
            _context.prev = 6;
            _context.next = 9;
            return fetch(form.action, {
              method: 'POST',
              body: formData,
              headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

          case 9:
            response = _context.sent;
            _context.next = 12;
            return response.json();

          case 12:
            data = _context.sent;

            if (data.success) {
              // Clear the message input
              messageInput = form.querySelector('textarea[name="message"]');
              if (messageInput) messageInput.value = ''; // Clear file selection

              fileInput = form.querySelector('input[type="file"]');
              if (fileInput) fileInput.value = ''; // Clear selected files display

              selectedFiles = document.getElementById('selected-files');
              if (selectedFiles) selectedFiles.innerHTML = ''; // Immediately append the message to the UI if message data is returned

              if (data.message && window.threadManagement) {
                window.threadManagement.appendNewMessage(data.message);
              }

              console.log('Message sent successfully'); // Note: Echo will also handle real-time updates for other users
            } else {
              alert('Error sending message: ' + (data.message || 'Unknown error'));
            }

            _context.next = 20;
            break;

          case 16:
            _context.prev = 16;
            _context.t0 = _context["catch"](6);
            console.error('Error:', _context.t0);
            alert('Error sending message. Please try again.');

          case 20:
            _context.prev = 20;
            submitButton.disabled = false;
            return _context.finish(20);

          case 23:
          case "end":
            return _context.stop();
        }
      }
    }, _callee, null, [[6, 16, 20, 23]]);
  }));

  return function (_x) {
    return _ref.apply(this, arguments);
  };
}(); // Handle file selection for message attachments


window.handleFileSelection = function (input) {
  var fileList = input.files;
  var previewContainer = document.getElementById('selected-files');
  if (!previewContainer) return;
  previewContainer.innerHTML = '';

  for (var i = 0; i < fileList.length; i++) {
    var file = fileList[i];
    var fileSize = (file.size / 1024).toFixed(1) + ' KB';
    var filePreview = document.createElement('div');
    filePreview.className = 'bg-gray-100 rounded-md p-2 flex items-center justify-between';
    filePreview.innerHTML = "\n      <div class=\"flex items-center\">\n        <svg class=\"w-4 h-4 mr-2 text-gray-500\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n          <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13\"></path>\n        </svg>\n        <span class=\"text-xs truncate max-w-[150px]\">".concat(file.name, "</span>\n        <span class=\"text-xs text-gray-500 ml-2\">").concat(fileSize, "</span>\n      </div>\n      <button type=\"button\" class=\"text-gray-500 hover:text-red-500\" onclick=\"this.parentNode.remove()\">\n        <svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n          <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M6 18L18 6M6 6l12 12\"></path>\n        </svg>\n      </button>\n    ");
    previewContainer.appendChild(filePreview);
  }
}; // Global function to scroll to bottom of message list


window.scrollToBottom = function () {
  var messageList = document.querySelector('.message-list');

  if (messageList) {
    messageList.scrollTop = messageList.scrollHeight;
  }
};
/**
 * Initialize Select2 for notification modal
 */


window.initializeNotificationSelect2 = function () {
  if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined' && document.getElementById('select-users')) {
    $('#select-users').select2({
      placeholder: 'Select users',
      allowClear: true,
      dropdownParent: $('#newNotificationModal'),
      ajax: {
        url: '/search-users',
        dataType: 'json',
        delay: 250,
        data: function data(params) {
          return {
            q: params.term
          };
        },
        processResults: function processResults(data) {
          return {
            results: data.results
          };
        },
        cache: true
      }
    });
  }
};
/**
 * Initialize Select2 for chat modal
 */


window.initializeChatSelect2 = function () {
  if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined' && document.getElementById('chat-user-select')) {
    $('#chat-user-select').select2({
      placeholder: 'Select a user...',
      allowClear: true,
      dropdownParent: $('#newChatModal'),
      ajax: {
        url: '/search-users',
        dataType: 'json',
        delay: 250,
        data: function data(params) {
          return {
            q: params.term
          };
        },
        processResults: function processResults(data) {
          return {
            results: data.results
          };
        },
        cache: true
      }
    });
  }
}; // Initialize event listeners when the DOM is loaded


document.addEventListener('DOMContentLoaded', function () {
  // Initialize recipient type toggle for notification modal
  var recipientTypeRadios = document.querySelectorAll('input[name="recipient_type"]');

  if (recipientTypeRadios.length > 0) {
    recipientTypeRadios.forEach(function (radio) {
      radio.addEventListener('change', function () {
        // Handle notification modal
        var userSelection = document.getElementById('user-selection');
        var segmentSelection = document.getElementById('segment-selection');

        if (userSelection && segmentSelection) {
          if (this.value === 'users') {
            userSelection.classList.remove('hidden');
            segmentSelection.classList.add('hidden');
          } else {
            userSelection.classList.add('hidden');
            segmentSelection.classList.remove('hidden');
          }
        } // Handle chat modal


        var chatUserSelection = document.getElementById('chat-user-selection');
        var chatSegmentSelection = document.getElementById('chat-segment-selection');

        if (chatUserSelection && chatSegmentSelection) {
          if (this.value === 'user') {
            chatUserSelection.classList.remove('hidden');
            chatSegmentSelection.classList.add('hidden'); // Make user selection required

            var userSelect = document.getElementById('chat-user-select');
            if (userSelect) userSelect.required = true; // Make segment selection not required

            var segmentSelect = document.getElementById('chat-segment');
            if (segmentSelect) segmentSelect.required = false;
          } else {
            chatUserSelection.classList.add('hidden');
            chatSegmentSelection.classList.remove('hidden'); // Make segment selection required

            var _segmentSelect = document.getElementById('chat-segment');

            if (_segmentSelect) _segmentSelect.required = true; // Make user selection not required

            var _userSelect = document.getElementById('chat-user-select');

            if (_userSelect) _userSelect.required = false;
          }
        }
      });
    });
  } // Initialize thread management when the DOM is loaded


  if (window.Echo) {
    window.threadManagement.init();
  } else {
    console.warn('Laravel Echo not found at DOMContentLoaded, delaying threadManagement init.');
    setTimeout(function () {
      if (window.Echo) {
        window.threadManagement.init();
      } else {
        console.error('Laravel Echo failed to initialize.');
      }
    }, 1000);
  }
});
})();

/******/ })()
;