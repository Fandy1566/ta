import './bootstrap';

import Alpine from 'alpinejs';
// import * as ort from 'onnxruntime-web';

// ort.env.wasm.wasmPaths = {
//   'ort-wasm.wasm': '/onnxruntime-web/dist/ort-wasm.wasm',
//   'ort-wasm-simd.wasm': '/onnxruntime-web/dist/ort-wasm-simd.wasm',
//   'ort-wasm-threaded.wasm': '/onnxruntime-web/dist/ort-wasm-threaded.wasm',
//   // Add other WASM files as needed
// };

window.Alpine = Alpine;
// window.ort = ort;

Alpine.start();
