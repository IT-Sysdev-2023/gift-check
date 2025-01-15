import figlet from "figlet";
import chalk from "chalk";

// Generate and display ASCII art
const asciiArt = figlet.textSync("GC - WAY CHANCE", { horizontalLayout: "default" });
console.log(chalk.blue(asciiArt));

// Display a message before starting the build
console.log(chalk.green("Starting the build process of your GC..."));
