const constants = new Uint32Array([
  0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
  0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
  0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
  0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
  0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
  0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
  0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
  0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2,
]);
const rotate = (value, bits) => (value >>> bits) | (value << (32 - bits));

// Incremental SHA-256 keeps large files bounded in memory, including HTTP pages
// where Web Crypto is unavailable because the origin is not a secure context.
export async function hashUploadFile(file, isAborted = () => false) {
  const state = new Uint32Array([0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19]);
  const words = new Uint32Array(64);
  const block = (bytes, offset) => {
    for (let i = 0; i < 16; i += 1) {
      const position = offset + (i * 4);
      words[i] = (bytes[position] << 24) | (bytes[position + 1] << 16) | (bytes[position + 2] << 8) | bytes[position + 3];
    }
    for (let i = 16; i < 64; i += 1) {
      const a = words[i - 15];
      const b = words[i - 2];
      words[i] = words[i - 16] + (rotate(a, 7) ^ rotate(a, 18) ^ (a >>> 3)) + words[i - 7] + (rotate(b, 17) ^ rotate(b, 19) ^ (b >>> 10));
    }
    let a = state[0]; let b = state[1]; let c = state[2]; let d = state[3];
    let e = state[4]; let f = state[5]; let g = state[6]; let h = state[7];
    for (let i = 0; i < 64; i += 1) {
      const first = (h + (rotate(e, 6) ^ rotate(e, 11) ^ rotate(e, 25)) + ((e & f) ^ (~e & g)) + constants[i] + words[i]) >>> 0;
      const second = ((rotate(a, 2) ^ rotate(a, 13) ^ rotate(a, 22)) + ((a & b) ^ (a & c) ^ (b & c))) >>> 0;
      h = g; g = f; f = e; e = (d + first) >>> 0;
      d = c; c = b; b = a; a = (first + second) >>> 0;
    }
    state[0] += a; state[1] += b; state[2] += c; state[3] += d;
    state[4] += e; state[5] += f; state[6] += g; state[7] += h;
  };
  const readSize = 1024 * 1024;
  let tail = new Uint8Array(0);
  for (let offset = 0; offset < file.size; offset += readSize) {
    if (isAborted()) throw new Error('Upload dibatalkan.');
    const bytes = new Uint8Array(await file.slice(offset, offset + readSize).arrayBuffer());
    let position = 0;
    for (; position + 64 <= bytes.length; position += 64) block(bytes, position);
    tail = bytes.slice(position);
    if (offset + readSize < file.size) await new Promise((resolve) => setTimeout(resolve, 0));
  }
  const padding = new Uint8Array(tail.length < 56 ? 64 : 128);
  padding.set(tail);
  padding[tail.length] = 0x80;
  const view = new DataView(padding.buffer);
  view.setUint32(padding.length - 8, Math.floor(file.size / 0x20000000));
  view.setUint32(padding.length - 4, (file.size * 8) >>> 0);
  for (let offset = 0; offset < padding.length; offset += 64) block(padding, offset);
  return Array.from(state, (value) => value.toString(16).padStart(8, '0')).join('');
}
