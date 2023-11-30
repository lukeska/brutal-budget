module.exports = {
    "env": {
        "browser": true,
        "es2021": true
    },
    "extends": [
        "eslint:recommended",
        "plugin:@typescript-eslint/eslint-recommended",
        "plugin:vue/vue3-essential",
        "prettier"
    ],
    "overrides": [
        {
            "env": {
                "node": true
            },
            "files": [
                ".eslintrc.{js,cjs}"
            ],
            "parserOptions": {
                "sourceType": "script"
            }
        }
    ],
    "parserOptions": {
        "ecmaVersion": "latest",
        "parser": "@typescript-eslint/parser",
        "sourceType": "module"
    },
    "plugins": [
        "@typescript-eslint",
        "vue"
    ],
    "rules": {
        "vue/multi-word-component-names": "off",
        // "vue/component-api-style": ["error",
        //     ["script-setup", "composition"]
        // ],
        "vue/component-name-in-template-casing": "error",
        "vue/block-lang": ["error", {script: {lang: "ts"}}],
        "vue/define-macros-order": ["error", {
            order: ["defineProps", "defineEmits"],
        }],
        "vue/define-emits-declaration": ["error", "type-based"],
        "vue/define-props-declaration": ["error", "type-based"],
        "vue/no-undef-components": "error",
        "vue/no-unused-refs": "error",
        "vue/no-v-html": "off",
        "no-undef": "off",
        "no-unused-vars": "off",
    },
    "parser": "vue-eslint-parser",
    "ignorePatterns": ["*.d.ts", "*.m.js"],
}
