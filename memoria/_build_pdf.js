const fs = require('fs');
const path = require('path');
const { marked } = require('marked');

const md = fs.readFileSync(path.join(__dirname, 'MEMORIA.md'), 'utf8');
const body = marked.parse(md);

const css = `
@page { size: A4; margin: 22mm 18mm; }
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; }
body {
  font-family: 'Georgia', 'Times New Roman', serif;
  color: #2b2a28;
  font-size: 11pt;
  line-height: 1.55;
}
h1, h2, h3, h4 {
  font-family: 'Georgia', 'Times New Roman', serif;
  color: #5b5248;
  page-break-after: avoid;
}
h1 {
  font-size: 24pt;
  border-bottom: 1.5px solid #c6a87d;
  padding-bottom: 8px;
  margin-top: 28px;
}
h2 {
  font-size: 16pt;
  color: #c6a87d;
  border-bottom: 1px solid #e6dac7;
  padding-bottom: 4px;
  margin-top: 26px;
}
h3 { font-size: 13pt; margin-top: 20px; }
h4 { font-size: 11.5pt; color: #2b2a28; }
p { margin: 0.5em 0; text-align: justify; }
strong { color: #5b5248; }
a { color: #a8893f; text-decoration: none; }
ul, ol { padding-left: 22px; margin: 0.4em 0 0.8em; }
li { margin-bottom: 0.25em; }
blockquote {
  border-left: 3px solid #c6a87d;
  padding: 6px 14px;
  margin: 12px 0;
  background: #faf8f5;
  color: #5b5248;
  font-style: italic;
}
code {
  font-family: 'Consolas', 'Courier New', monospace;
  background: #f4efe6;
  color: #5b5248;
  padding: 1px 5px;
  border-radius: 3px;
  font-size: 9.5pt;
}
pre {
  background: #fbf8f2;
  border: 1px solid #e6dac7;
  border-radius: 4px;
  padding: 10px 14px;
  font-size: 9pt;
  overflow-x: auto;
  page-break-inside: avoid;
  font-family: 'Consolas', 'Courier New', monospace;
}
pre code { background: transparent; padding: 0; color: #2b2a28; }
table {
  border-collapse: collapse;
  width: 100%;
  margin: 12px 0;
  font-size: 9.5pt;
  page-break-inside: avoid;
}
th, td {
  border: 1px solid #e0d4bc;
  padding: 6px 9px;
  text-align: left;
  vertical-align: top;
}
th { background: #faf3e6; color: #5b5248; font-weight: 600; }
tr:nth-child(even) td { background: #fbf9f4; }
hr {
  border: none;
  border-top: 1px solid #e6dac7;
  margin: 24px 0;
}
.portada {
  text-align: center;
  padding: 80px 30px 60px;
  border: 1px solid #c6a87d;
  margin-bottom: 40px;
  page-break-after: always;
}
.portada h1 {
  font-size: 36pt;
  border: none;
  margin: 0 0 8px;
  letter-spacing: 0.05em;
}
.portada .subtitulo {
  color: #c6a87d;
  font-style: italic;
  font-size: 13pt;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}
.portada .meta {
  margin-top: 80px;
  color: #5b5248;
  line-height: 1.9;
}
`;

const html = `<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Memoria · Eternamente</title>
<style>${css}</style>
</head>
<body>
${body}
</body>
</html>`;

fs.writeFileSync(path.join(__dirname, '_memoria.html'), html, 'utf8');
console.log('OK: _memoria.html generado');
