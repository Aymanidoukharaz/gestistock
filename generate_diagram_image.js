const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');

(async () => {
    const browser = await puppeteer.launch({ timeout: 60000 }); // Augmenter le délai à 60 secondes
    const page = await browser.newPage();

    const inputFilePath = path.resolve(process.argv[2]);
    const outputImagePath = path.resolve(process.argv[3]);

    if (!inputFilePath || !outputImagePath) {
        console.error('Usage: node generate_diagram_image.js <inputFilePath.mmd> <outputImagePath.png>');
        await browser.close();
        return;
    }

    const mmdContent = fs.readFileSync(inputFilePath, 'utf8');

    await page.setContent(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Mermaid Diagram</title>
            <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
            <style>
                body {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    background-color: #ffffff; /* White background for the image */
                }
            </style>
        </head>
        <body>
            <div class="mermaid">
                ${mmdContent}
            </div>
            <script>
                mermaid.initialize({ startOnLoad: true });
            </script>
        </body>
        </html>
    `, { waitUntil: 'networkidle0' });

    // Wait for Mermaid to render the diagram
    await page.waitForSelector('.mermaid svg');

    const diagram = await page.$('.mermaid');
    if (diagram) {
        await diagram.screenshot({ path: outputImagePath, omitBackground: true });
        console.log(`Diagram saved to ${outputImagePath}`);
    } else {
        console.error('Mermaid diagram not found on the page.');
    }

    await browser.close();
})();