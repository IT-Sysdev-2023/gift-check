// vite.config.js
import { defineConfig } from "file:///C:/laragon/www/gift-check/node_modules/.pnpm/vite@5.3.4/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/laragon/www/gift-check/node_modules/.pnpm/laravel-vite-plugin@1.0.5_vite@5.3.4/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/laragon/www/gift-check/node_modules/.pnpm/@vitejs+plugin-vue@5.0.5_vite@5.3.4_vue@3.4.32_typescript@5.5.3_/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import Components from "file:///C:/laragon/www/gift-check/node_modules/.pnpm/unplugin-vue-components@0.27.3_@babel+parser@7.24.8_rollup@4.18.1_vue@3.4.32_typescript@5.5.3_/node_modules/unplugin-vue-components/dist/vite.js";
import { AntDesignVueResolver } from "file:///C:/laragon/www/gift-check/node_modules/.pnpm/unplugin-vue-components@0.27.3_@babel+parser@7.24.8_rollup@4.18.1_vue@3.4.32_typescript@5.5.3_/node_modules/unplugin-vue-components/dist/resolvers.js";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: "resources/js/app.ts",
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    Components({
      resolvers: [
        AntDesignVueResolver({ resolveIcons: true, importStyle: false }),
        (componentName) => {
          if (["Head", "Link"].includes(componentName)) {
            return { name: componentName, from: "@inertiajs/vue3" };
          }
        }
      ],
      dirs: [
        "resources/js/Components",
        "resources/js/Layouts",
        "resources/js/Pages"
      ]
    })
  ]
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFxnaWZ0LWNoZWNrXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJDOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFxnaWZ0LWNoZWNrXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9DOi9sYXJhZ29uL3d3dy9naWZ0LWNoZWNrL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJztcbmltcG9ydCB2dWUgZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlJztcbmltcG9ydCBDb21wb25lbnRzIGZyb20gJ3VucGx1Z2luLXZ1ZS1jb21wb25lbnRzL3ZpdGUnO1xuaW1wb3J0IHsgQW50RGVzaWduVnVlUmVzb2x2ZXIgfSBmcm9tICd1bnBsdWdpbi12dWUtY29tcG9uZW50cy9yZXNvbHZlcnMnO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICAgXG4gICAgcGx1Z2luczogW1xuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiAncmVzb3VyY2VzL2pzL2FwcC50cycsXG4gICAgICAgICAgICByZWZyZXNoOiB0cnVlLFxuICAgICAgICB9KSxcbiAgICAgICAgdnVlKHtcbiAgICAgICAgICAgIHRlbXBsYXRlOiB7XG4gICAgICAgICAgICAgICAgdHJhbnNmb3JtQXNzZXRVcmxzOiB7XG4gICAgICAgICAgICAgICAgICAgIGJhc2U6IG51bGwsXG4gICAgICAgICAgICAgICAgICAgIGluY2x1ZGVBYnNvbHV0ZTogZmFsc2UsXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIH0sXG4gICAgICAgIH0pLFxuXG4gICAgICAgIENvbXBvbmVudHMoe1xuICAgICAgICAgICAgcmVzb2x2ZXJzOiBbXG4gICAgICAgICAgICAgICAgQW50RGVzaWduVnVlUmVzb2x2ZXIoeyAgcmVzb2x2ZUljb25zOiB0cnVlLCBpbXBvcnRTdHlsZTogZmFsc2UgfSksIFxuXHRcdFx0XHQoY29tcG9uZW50TmFtZSkgPT4ge1xuXHRcdFx0XHRcdGlmIChbJ0hlYWQnLCAnTGluayddLmluY2x1ZGVzKGNvbXBvbmVudE5hbWUpKSB7XG5cdFx0XHRcdFx0XHRyZXR1cm4geyBuYW1lOiBjb21wb25lbnROYW1lLCBmcm9tOiAnQGluZXJ0aWFqcy92dWUzJyB9XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG4gICAgICAgICAgICBdLFxuICAgICAgICAgICAgZGlyczogWyBcblx0XHRcdFx0J3Jlc291cmNlcy9qcy9Db21wb25lbnRzJyxcblx0XHRcdFx0J3Jlc291cmNlcy9qcy9MYXlvdXRzJyxcblx0XHRcdFx0J3Jlc291cmNlcy9qcy9QYWdlcycsXG5cdFx0XHRdXG4gICAgICAgICAgfSksXG4gICAgXSxcbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFxUSxTQUFTLG9CQUFvQjtBQUNsUyxPQUFPLGFBQWE7QUFDcEIsT0FBTyxTQUFTO0FBQ2hCLE9BQU8sZ0JBQWdCO0FBQ3ZCLFNBQVMsNEJBQTRCO0FBRXJDLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBRXhCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQSxNQUNQLFNBQVM7QUFBQSxJQUNiLENBQUM7QUFBQSxJQUNELElBQUk7QUFBQSxNQUNBLFVBQVU7QUFBQSxRQUNOLG9CQUFvQjtBQUFBLFVBQ2hCLE1BQU07QUFBQSxVQUNOLGlCQUFpQjtBQUFBLFFBQ3JCO0FBQUEsTUFDSjtBQUFBLElBQ0osQ0FBQztBQUFBLElBRUQsV0FBVztBQUFBLE1BQ1AsV0FBVztBQUFBLFFBQ1AscUJBQXFCLEVBQUcsY0FBYyxNQUFNLGFBQWEsTUFBTSxDQUFDO0FBQUEsUUFDNUUsQ0FBQyxrQkFBa0I7QUFDbEIsY0FBSSxDQUFDLFFBQVEsTUFBTSxFQUFFLFNBQVMsYUFBYSxHQUFHO0FBQzdDLG1CQUFPLEVBQUUsTUFBTSxlQUFlLE1BQU0sa0JBQWtCO0FBQUEsVUFDdkQ7QUFBQSxRQUNEO0FBQUEsTUFDUTtBQUFBLE1BQ0EsTUFBTTtBQUFBLFFBQ2Q7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLE1BQ0Q7QUFBQSxJQUNPLENBQUM7QUFBQSxFQUNQO0FBQ0osQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
