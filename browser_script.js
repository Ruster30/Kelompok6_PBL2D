// Import browser client
const { setupBrowserRuntime } = await import("C:\Users\ASUS\.codex\plugins\cache\openai-bundled\browser\26.623.141536/scripts/browser-client.mjs");
await setupBrowserRuntime({ globals: globalThis });

// Navigate to login page
const page = await agent.browsers.default.openPage("http://127.0.0.1:8080/login");
await page.waitForLoadState('networkidle');
console.log("Page title:", await page.title());

// Take screenshot
await page.screenshot({ path: "C:\\Users\\ASUS\\AppData\\Local\\Temp\\analytics-login.png" });
console.log("Screenshot saved");
