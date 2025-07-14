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
/*!**********************************!*\
  !*** ./resources/js/segments.js ***!
  \**********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

// Set the base URL for API calls
var baseApiUrl = ''; // Store the active tab in session storage

function saveActiveTab(tabName) {
  sessionStorage.setItem('activeTab', tabName);
} // Get the active tab from session storage


function getActiveTab() {
  return sessionStorage.getItem('activeTab') || 'members';
}

document.addEventListener('DOMContentLoaded', function () {
  var _document$querySelect;

  // Get the business ID from the data attribute
  var businessId = (_document$querySelect = document.querySelector('meta[name="business-id"]')) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.content;
  baseApiUrl = "/members/".concat(businessId); // Restore active tab from session storage

  var activeTab = getActiveTab();

  if (activeTab) {
    switchTab(activeTab);
  } // Handle segment creation form submission


  var createSegmentForm = document.getElementById('createSegmentForm');

  if (createSegmentForm) {
    createSegmentForm.addEventListener('submit', /*#__PURE__*/function () {
      var _ref = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee(e) {
        var form, formData, response, data;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                e.preventDefault();
                form = e.target;
                formData = new FormData(form);
                _context.prev = 3;
                _context.next = 6;
                return fetch("".concat(baseApiUrl, "/segments"), {
                  method: 'POST',
                  headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                  },
                  body: formData
                });

              case 6:
                response = _context.sent;

                if (response.ok) {
                  _context.next = 9;
                  break;
                }

                throw new Error("HTTP error: ".concat(response.status));

              case 9:
                _context.next = 11;
                return response.json();

              case 11:
                data = _context.sent;

                if (data.success) {
                  // Reload with fragment to maintain tab state
                  window.location.href = window.location.pathname + '?tab=segments';
                } else {
                  alert(data.message || 'Error creating segment');
                }

                _context.next = 19;
                break;

              case 15:
                _context.prev = 15;
                _context.t0 = _context["catch"](3);
                console.error('Error creating segment:', _context.t0);
                alert('Failed to create segment. Please try again.');

              case 19:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, null, [[3, 15]]);
      }));

      return function (_x) {
        return _ref.apply(this, arguments);
      };
    }());
  } // Add event listeners for tab switching if we're on the members page


  var mobileTabSelect = document.getElementById('mobile-tabs');

  if (mobileTabSelect) {
    mobileTabSelect.addEventListener('change', function () {
      switchTab(this.value);
    });
  } // Initialize segment item data attributes


  document.querySelectorAll('.segment-item').forEach(function (item) {
    var nameEl = item.querySelector('p.text-indigo-600');
    var typeEl = item.querySelector('span[class*="bg-"]');

    if (nameEl && typeEl) {
      nameEl.classList.add('segment-name');
      typeEl.classList.add('segment-type');
    }
  }); // Check URL parameter for tab selection

  var urlParams = new URLSearchParams(window.location.search);
  var tabParam = urlParams.get('tab');

  if (tabParam) {
    switchTab(tabParam);
  }
}); // Function to handle tab switching

window.switchTab = function (tabName) {
  // Hide all tab contents
  document.querySelectorAll('.tab-content').forEach(function (content) {
    content.classList.add('hidden');
  }); // Show selected tab content

  document.getElementById(tabName + '-tab').classList.remove('hidden'); // Update tab button styles

  document.querySelectorAll('.tab-button').forEach(function (button) {
    if (button.dataset.tab === tabName) {
      button.classList.add('border-indigo-500', 'text-indigo-600');
      button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    } else {
      button.classList.remove('border-indigo-500', 'text-indigo-600');
      button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    }
  }); // Save active tab to session storage

  saveActiveTab(tabName); // Update mobile tab selector if it exists

  var mobileTabSelect = document.getElementById('mobile-tabs');

  if (mobileTabSelect) {
    mobileTabSelect.value = tabName;
  } // Update URL with the tab parameter without reloading the page


  var url = new URL(window.location);
  url.searchParams.set('tab', tabName);
  window.history.pushState({}, '', url);
}; // Function to view segment members with pagination


window.viewSegmentMembers = /*#__PURE__*/function () {
  var _ref2 = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee3(segmentId) {
    var response, data, modalHtml, modalContainer, membersList, offset, limit, loadingIndicator;
    return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee3$(_context3) {
      while (1) {
        switch (_context3.prev = _context3.next) {
          case 0:
            _context3.prev = 0;
            _context3.next = 3;
            return fetch("".concat(baseApiUrl, "/segments/").concat(segmentId, "/preview"), {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

          case 3:
            response = _context3.sent;

            if (response.ok) {
              _context3.next = 6;
              break;
            }

            throw new Error("HTTP error: ".concat(response.status));

          case 6:
            _context3.next = 8;
            return response.json();

          case 8:
            data = _context3.sent;
            // Create and show modal with users list
            modalHtml = "\n            <div class=\"fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity\"></div>\n            <div class=\"fixed inset-0 z-10 overflow-y-auto\">\n                <div class=\"flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0\">\n                    <div class=\"relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6\">\n                        <div class=\"absolute right-0 top-0 pr-4 pt-4\">\n                            <button type=\"button\" class=\"close-modal rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2\">\n                                <span class=\"sr-only\">Close</span>\n                                <svg class=\"h-6 w-6\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\">\n                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M6 18L18 6M6 6l12 12\" />\n                                </svg>\n                            </button>\n                        </div>\n                        <div class=\"sm:flex sm:items-start\">\n                            <div class=\"mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full\">\n                                <h3 class=\"text-base font-semibold leading-6 text-gray-900\">Segment Members (".concat(data.count, ")</h3>\n                                <div class=\"mt-2\">\n                                    <ul class=\"divide-y divide-gray-200 max-h-96 overflow-y-auto\" id=\"segment-members-list\">\n                                        ").concat(data.users.map(function (user) {
              return "\n                                            <li class=\"py-4\">\n                                                <div class=\"flex items-center space-x-4\">\n                                                    <div class=\"flex-1 min-w-0\">\n                                                        <p class=\"text-sm font-medium text-gray-900 truncate\">\n                                                            ".concat(user.first_name, " ").concat(user.last_name, "\n                                                        </p>\n                                                        <p class=\"text-sm text-gray-500 truncate\">\n                                                            ").concat(user.email, "\n                                                        </p>\n                                                    </div>\n                                                </div>\n                                            </li>\n                                        ");
            }).join(''), "\n                                    </ul>\n                                </div>\n                                <div class=\"mt-4 flex justify-center\" id=\"members-loading-more\" style=\"display: none;\">\n                                    <div class=\"loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-8 w-8\"></div>\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        ");
            modalContainer = document.getElementById('modal-container');

            if (modalContainer) {
              modalContainer.innerHTML = modalHtml;
              modalContainer.classList.remove('hidden'); // Handle closing modal

              modalContainer.querySelectorAll('.close-modal').forEach(function (button) {
                button.addEventListener('click', function () {
                  modalContainer.classList.add('hidden');
                  modalContainer.innerHTML = '';
                });
              }); // Implement infinite scroll for members list

              membersList = document.getElementById('segment-members-list');
              offset = data.users.length;
              limit = 20;
              loadingIndicator = document.getElementById('members-loading-more'); // Simple infinite scroll implementation

              if (membersList) {
                membersList.addEventListener('scroll', /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee2() {
                  var moreResponse, moreData, fragment;
                  return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee2$(_context2) {
                    while (1) {
                      switch (_context2.prev = _context2.next) {
                        case 0:
                          if (!(membersList.scrollTop + membersList.clientHeight >= membersList.scrollHeight - 50)) {
                            _context2.next = 21;
                            break;
                          }

                          if (!(loadingIndicator.style.display === 'none' && offset < data.count)) {
                            _context2.next = 21;
                            break;
                          }

                          loadingIndicator.style.display = 'block';
                          _context2.prev = 3;
                          _context2.next = 6;
                          return fetch("".concat(baseApiUrl, "/segments/").concat(segmentId, "/preview?offset=").concat(offset, "&limit=").concat(limit), {
                            headers: {
                              'Accept': 'application/json',
                              'X-Requested-With': 'XMLHttpRequest'
                            }
                          });

                        case 6:
                          moreResponse = _context2.sent;

                          if (moreResponse.ok) {
                            _context2.next = 9;
                            break;
                          }

                          throw new Error('Failed to load more members');

                        case 9:
                          _context2.next = 11;
                          return moreResponse.json();

                        case 11:
                          moreData = _context2.sent;

                          if (moreData.users.length > 0) {
                            fragment = document.createDocumentFragment();
                            moreData.users.forEach(function (user) {
                              var li = document.createElement('li');
                              li.className = 'py-4';
                              li.innerHTML = "\n                                            <div class=\"flex items-center space-x-4\">\n                                                <div class=\"flex-1 min-w-0\">\n                                                    <p class=\"text-sm font-medium text-gray-900 truncate\">\n                                                        ".concat(user.first_name, " ").concat(user.last_name, "\n                                                    </p>\n                                                    <p class=\"text-sm text-gray-500 truncate\">\n                                                        ").concat(user.email, "\n                                                    </p>\n                                                </div>\n                                            </div>\n                                        ");
                              fragment.appendChild(li);
                            });
                            membersList.appendChild(fragment);
                            offset += moreData.users.length;
                          }

                          _context2.next = 18;
                          break;

                        case 15:
                          _context2.prev = 15;
                          _context2.t0 = _context2["catch"](3);
                          console.error('Error loading more members:', _context2.t0);

                        case 18:
                          _context2.prev = 18;
                          loadingIndicator.style.display = 'none';
                          return _context2.finish(18);

                        case 21:
                        case "end":
                          return _context2.stop();
                      }
                    }
                  }, _callee2, null, [[3, 15, 18, 21]]);
                })));
              }
            }

            _context3.next = 18;
            break;

          case 14:
            _context3.prev = 14;
            _context3.t0 = _context3["catch"](0);
            console.error('Error fetching segment members:', _context3.t0);
            alert('Failed to load segment members. Please try again.');

          case 18:
          case "end":
            return _context3.stop();
        }
      }
    }, _callee3, null, [[0, 14]]);
  }));

  return function (_x2) {
    return _ref2.apply(this, arguments);
  };
}(); // Function for editing segments


window.editSegment = /*#__PURE__*/function () {
  var _ref4 = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee5(segmentId) {
    var _segmentItem$querySel, _document$querySelect2, segmentItem, name, description, type, businessId, url, modalHtml, modalContainer, form;

    return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee5$(_context5) {
      while (1) {
        switch (_context5.prev = _context5.next) {
          case 0:
            _context5.prev = 0;
            // Get segment data from the DOM
            segmentItem = document.querySelector(".segment-item[data-segment-id=\"".concat(segmentId, "\"]"));

            if (segmentItem) {
              _context5.next = 4;
              break;
            }

            throw new Error("Segment element not found");

          case 4:
            name = segmentItem.querySelector('.segment-name').textContent.trim();
            description = ((_segmentItem$querySel = segmentItem.querySelector('.segment-description')) === null || _segmentItem$querySel === void 0 ? void 0 : _segmentItem$querySel.textContent.trim()) || '';
            type = segmentItem.querySelector('.segment-type').textContent.trim().toLowerCase();
            businessId = (_document$querySelect2 = document.querySelector('meta[name="business-id"]')) === null || _document$querySelect2 === void 0 ? void 0 : _document$querySelect2.content;
            url = "/members/".concat(businessId, "/segments/").concat(segmentId); // Create and show edit modal

            modalHtml = "\n            <div class=\"fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity\"></div>\n            <div class=\"fixed inset-0 z-10 overflow-y-auto\">\n                <div class=\"flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0\">\n                    <div class=\"relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6\">\n                        <form id=\"editSegmentForm\" action=".concat(url, " >\n                            <input type=\"hidden\" name=\"_token\" value=\"").concat(document.querySelector('meta[name="csrf-token"]').content, "\">\n                            <div class=\"space-y-4\">\n                                <div>\n                                    <label for=\"edit-name\" class=\"block text-sm font-medium text-gray-700\">Name</label>\n                                    <input type=\"text\" name=\"name\" id=\"edit-name\" value=\"").concat(name, "\" required\n                                        class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm\">\n                                </div>\n                                <div>\n                                    <label for=\"edit-description\" class=\"block text-sm font-medium text-gray-700\">Description</label>\n                                    <textarea name=\"description\" id=\"edit-description\" rows=\"3\"\n                                        class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm\">").concat(description, "</textarea>\n                                </div>\n                                <div>\n                                    <label for=\"edit-type\" class=\"block text-sm font-medium text-gray-700\">Type</label>\n                                    <select name=\"type\" id=\"edit-type\" required\n                                        class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm\">\n                                        <option value=\"custom\" ").concat(type === 'custom' ? 'selected' : '', ">Custom</option>\n                                        <option value=\"member\" ").concat(type === 'member' ? 'selected' : '', ">Member</option>\n                                        <option value=\"admin\" ").concat(type === 'admin' ? 'selected' : '', ">Admin</option>\n                                    </select>\n                                </div>\n                            </div>\n                            <div class=\"mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3\">\n                                <button type=\"submit\"\n                                    class=\"inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2\">\n                                    Save Changes\n                                </button>\n                                <button type=\"button\" class=\"close-modal mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0\">\n                                    Cancel\n                                </button>\n                            </div>\n                        </form>\n                    </div>\n                </div>\n            </div>\n        ");
            modalContainer = document.getElementById('modal-container');
            modalContainer.innerHTML = modalHtml;
            modalContainer.classList.remove('hidden'); // Handle form submission

            form = modalContainer.querySelector('form');
            form.addEventListener('submit', /*#__PURE__*/function () {
              var _ref5 = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee4(e) {
                var formData, response, data;
                return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee4$(_context4) {
                  while (1) {
                    switch (_context4.prev = _context4.next) {
                      case 0:
                        e.preventDefault();
                        formData = new FormData(e.target);
                        formData.append('_method', 'PUT');
                        _context4.prev = 3;
                        console.log('url is ');
                        console.log(url);
                        _context4.next = 8;
                        return fetch("".concat(url), {
                          method: 'POST',
                          headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                          },
                          body: formData
                        });

                      case 8:
                        response = _context4.sent;
                        console.log('yeta tira aayo');

                        if (response.ok) {
                          _context4.next = 12;
                          break;
                        }

                        throw new Error("HTTP error: ".concat(response.status));

                      case 12:
                        _context4.next = 14;
                        return response.json();

                      case 14:
                        data = _context4.sent;

                        if (data.success) {
                          window.location.href = window.location.pathname + '?tab=segments';
                        } else {
                          alert(data.message || 'Error updating segment');
                        }

                        _context4.next = 22;
                        break;

                      case 18:
                        _context4.prev = 18;
                        _context4.t0 = _context4["catch"](3);
                        console.error('Error updating segment:', _context4.t0);
                        alert('Failed to update segment. Please try again.');

                      case 22:
                      case "end":
                        return _context4.stop();
                    }
                  }
                }, _callee4, null, [[3, 18]]);
              }));

              return function (_x4) {
                return _ref5.apply(this, arguments);
              };
            }()); // Handle modal closing

            modalContainer.querySelectorAll('.close-modal').forEach(function (button) {
              button.addEventListener('click', function () {
                modalContainer.classList.add('hidden');
                modalContainer.innerHTML = '';
              });
            });
            _context5.next = 22;
            break;

          case 18:
            _context5.prev = 18;
            _context5.t0 = _context5["catch"](0);
            console.error('Error editing segment:', _context5.t0);
            alert('Failed to edit segment. Please try again.');

          case 22:
          case "end":
            return _context5.stop();
        }
      }
    }, _callee5, null, [[0, 18]]);
  }));

  return function (_x3) {
    return _ref4.apply(this, arguments);
  };
}();

window.showAddUsersModal = function (segmentId) {
  var modal = document.getElementById('assign-users-modal-container');
  document.getElementById('assign-segment-id').value = segmentId; // Clear previous select2 if needed

  $('#assign-users-select').empty().select2({
    placeholder: 'Search users...',
    width: '100%',
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
  modal.classList.remove('hidden');
};

window.closeAssignUsersModal = function () {
  document.getElementById('assign-users-modal-container').classList.add('hidden');
  $('#assign-users-select').select2('destroy');
};

document.getElementById('assign-users-form').addEventListener('submit', /*#__PURE__*/function () {
  var _ref6 = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee6(e) {
    var _document$querySelect3;

    var segmentId, user_ids, businessId, response, result;
    return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee6$(_context6) {
      while (1) {
        switch (_context6.prev = _context6.next) {
          case 0:
            e.preventDefault();
            segmentId = document.getElementById('assign-segment-id').value;
            user_ids = $('#assign-users-select').val(); // Select2 selected user IDs

            businessId = (_document$querySelect3 = document.querySelector('meta[name="business-id"]')) === null || _document$querySelect3 === void 0 ? void 0 : _document$querySelect3.content;
            console.log(JSON.stringify({
              user_ids: user_ids
            }));
            _context6.prev = 5;
            _context6.next = 8;
            return fetch("/members/".concat(businessId, "/segments/").concat(segmentId, "/users"), {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                user_ids: user_ids
              })
            });

          case 8:
            response = _context6.sent;

            if (response.ok) {
              _context6.next = 11;
              break;
            }

            throw new Error('Request failed');

          case 11:
            _context6.next = 13;
            return response.json();

          case 13:
            result = _context6.sent;

            if (result.success) {
              alert('Users assigned successfully.');
              closeAssignUsersModal();
            } else {
              alert(result.message || 'Failed to assign users.');
            }

            _context6.next = 21;
            break;

          case 17:
            _context6.prev = 17;
            _context6.t0 = _context6["catch"](5);
            console.error(_context6.t0);
            alert('Error assigning users.');

          case 21:
          case "end":
            return _context6.stop();
        }
      }
    }, _callee6, null, [[5, 17]]);
  }));

  return function (_x5) {
    return _ref6.apply(this, arguments);
  };
}()); // Function to delete a segment

window.deleteSegment = /*#__PURE__*/function () {
  var _ref7 = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee7(segmentId) {
    var response, data;
    return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee7$(_context7) {
      while (1) {
        switch (_context7.prev = _context7.next) {
          case 0:
            if (confirm('Are you sure you want to delete this segment? This action cannot be undone.')) {
              _context7.next = 2;
              break;
            }

            return _context7.abrupt("return");

          case 2:
            _context7.prev = 2;
            _context7.next = 5;
            return fetch("".concat(baseApiUrl, "/segments/").concat(segmentId), {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

          case 5:
            response = _context7.sent;

            if (response.ok) {
              _context7.next = 8;
              break;
            }

            throw new Error("HTTP error: ".concat(response.status));

          case 8:
            _context7.next = 10;
            return response.json();

          case 10:
            data = _context7.sent;

            if (data.success) {
              window.location.href = window.location.pathname + '?tab=segments';
            } else {
              alert(data.message || 'Error deleting segment');
            }

            _context7.next = 18;
            break;

          case 14:
            _context7.prev = 14;
            _context7.t0 = _context7["catch"](2);
            console.error('Error deleting segment:', _context7.t0);
            alert('Failed to delete segment. Please try again.');

          case 18:
          case "end":
            return _context7.stop();
        }
      }
    }, _callee7, null, [[2, 14]]);
  }));

  return function (_x6) {
    return _ref7.apply(this, arguments);
  };
}(); // Functions for create/close segment form


window.openCreateSegmentForm = function () {
  var form = document.getElementById('create-segment-form');
  form.classList.remove('hidden');
};

window.closeCreateSegmentForm = function () {
  var form = document.getElementById('create-segment-form');
  form.classList.add('hidden');
}; // Function to assign segments to users


window.assignSegments = /*#__PURE__*/function () {
  var _ref8 = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee9(userId) {
    var _document$querySelect4, businessId, response, allSegments, userRow, userSegments, modalHtml, modalContainer, form;

    return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee9$(_context9) {
      while (1) {
        switch (_context9.prev = _context9.next) {
          case 0:
            _context9.prev = 0;
            businessId = (_document$querySelect4 = document.querySelector('meta[name="business-id"]')) === null || _document$querySelect4 === void 0 ? void 0 : _document$querySelect4.content; // Get all segments

            _context9.next = 4;
            return fetch("".concat(baseApiUrl, "/segments"), {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

          case 4:
            response = _context9.sent;

            if (response.ok) {
              _context9.next = 7;
              break;
            }

            throw new Error("HTTP error: ".concat(response.status));

          case 7:
            _context9.next = 9;
            return response.json();

          case 9:
            allSegments = _context9.sent;
            // Get user's current segments
            userRow = document.querySelector("[data-user-id=\"".concat(userId, "\"]"));
            userSegments = JSON.parse(userRow.dataset.segments || '[]'); // Create and show modal

            modalHtml = "\n            <div class=\"fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity\"></div>\n            <div class=\"fixed inset-0 z-10 overflow-y-auto\">\n                <div class=\"flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0\">\n                    <div class=\"relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6\">\n                        <div class=\"absolute right-0 top-0 pr-4 pt-4\">\n                            <button type=\"button\" class=\"close-modal rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2\">\n                                <span class=\"sr-only\">Close</span>\n                                <svg class=\"h-6 w-6\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\">\n                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M6 18L18 6M6 6l12 12\" />\n                                </svg>\n                            </button>\n                        </div>\n                        <form id=\"assignSegmentsForm\">\n                            <div class=\"space-y-4\">\n                                <h3 class=\"text-lg font-medium leading-6 text-gray-900\">Assign User to Segments</h3>\n                                <div class=\"mt-4 space-y-4\">\n                                    ".concat(allSegments.map(function (segment) {
              return "\n                                        <div class=\"relative flex items-start\">\n                                            <div class=\"flex h-6 items-center\">\n                                                <input type=\"checkbox\" name=\"segments[]\" value=\"".concat(segment.id, "\"\n                                                    ").concat(userSegments.includes(parseInt(segment.id)) ? 'checked' : '', "\n                                                    class=\"h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600\">\n                                            </div>\n                                            <div class=\"ml-3 text-sm leading-6\">\n                                                <label class=\"font-medium text-gray-900\">\n                                                    ").concat(segment.name, "\n                                                    <span class=\"text-gray-500\">(").concat(segment.type, ")</span>\n                                                </label>\n                                            </div>\n                                        </div>\n                                    ");
            }).join(''), "\n                                </div>\n                            </div>\n                            <div class=\"mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3\">\n                                <button type=\"submit\"\n                                    class=\"inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2\">\n                                    Save Changes\n                                </button>\n                                <button type=\"button\"\n                                    class=\"close-modal mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0\">\n                                    Cancel\n                                </button>\n                            </div>\n                        </form>\n                    </div>\n                </div>\n            </div>\n        ");
            modalContainer = document.getElementById('modal-container');
            modalContainer.innerHTML = modalHtml;
            modalContainer.classList.remove('hidden'); // Handle form submission

            form = modalContainer.querySelector('form');
            form.addEventListener('submit', /*#__PURE__*/function () {
              var _ref9 = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee8(e) {
                var formData, selectedSegments, updateResponse;
                return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee8$(_context8) {
                  while (1) {
                    switch (_context8.prev = _context8.next) {
                      case 0:
                        e.preventDefault();
                        formData = new FormData(e.target);
                        selectedSegments = formData.getAll('segments[]').map(function (id) {
                          return parseInt(id);
                        });
                        _context8.prev = 3;
                        _context8.next = 6;
                        return fetch("".concat(baseApiUrl, "/user-segments/").concat(userId), {
                          method: 'POST',
                          headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                          },
                          body: JSON.stringify({
                            segment_ids: selectedSegments
                          })
                        });

                      case 6:
                        updateResponse = _context8.sent;

                        if (updateResponse.ok) {
                          _context8.next = 9;
                          break;
                        }

                        throw new Error("HTTP error: ".concat(updateResponse.status));

                      case 9:
                        window.location.reload();
                        _context8.next = 16;
                        break;

                      case 12:
                        _context8.prev = 12;
                        _context8.t0 = _context8["catch"](3);
                        console.error('Error updating user segments:', _context8.t0);
                        alert('Failed to update user segments. Please try again.');

                      case 16:
                      case "end":
                        return _context8.stop();
                    }
                  }
                }, _callee8, null, [[3, 12]]);
              }));

              return function (_x8) {
                return _ref9.apply(this, arguments);
              };
            }()); // Handle modal closing

            modalContainer.querySelectorAll('.close-modal').forEach(function (button) {
              button.addEventListener('click', function () {
                modalContainer.classList.add('hidden');
                modalContainer.innerHTML = '';
              });
            });
            _context9.next = 25;
            break;

          case 21:
            _context9.prev = 21;
            _context9.t0 = _context9["catch"](0);
            console.error('Error assigning segments:', _context9.t0);
            alert('Failed to open segment assignment. Please try again.');

          case 25:
          case "end":
            return _context9.stop();
        }
      }
    }, _callee9, null, [[0, 21]]);
  }));

  return function (_x7) {
    return _ref8.apply(this, arguments);
  };
}();
})();

/******/ })()
;