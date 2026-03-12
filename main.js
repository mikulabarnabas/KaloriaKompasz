import { app, BrowserWindow } from "electron";
import path from "path";
import { fileURLToPath } from "url";

// Recreate __dirname for ES Modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

function createWindow() {
    const win = new BrowserWindow({
        width: 1000,
        height: 800,
        frame: false, // Removes the default Windows title bar
        titleBarStyle: "hidden", // Best for macOS/Windows overlay
        titleBarOverlay: {
            // Puts native buttons (Min/Max/Close) back in the corner
            color: "#1f2937", // Matches a Tailwind 'gray-800' background
            symbolColor: "#ffffff",
        },
        webPreferences: {
            nodeIntegration: true,
            contextIsolation: false,
        },
    });

    // Ensure this matches your Vite port (usually 5173 or 8000)
    win.loadURL("http://localhost:8000");
}

app.whenReady().then(() => {
    createWindow();

    app.on("activate", () => {
        if (BrowserWindow.getAllWindows().length === 0) {
            createWindow();
        }
    });
});

app.on("window-all-closed", () => {
    if (process.platform !== "darwin") {
        app.quit();
    }
});
