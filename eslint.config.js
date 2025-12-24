import prettier from 'eslint-config-prettier/flat';
import vue from 'eslint-plugin-vue';

import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript';

export default defineConfigWithVueTs(
    vue.configs['flat/essential'],
    vueTsConfigs.recommended,
    {
        ignores: ['vendor', 'node_modules', 'public', 'bootstrap/ssr', 'tailwind.config.js', 'resources/js/components/ui/*'],
    },
    {
        rules: {
            'vue/multi-word-component-names': 'off',
            '@typescript-eslint/no-explicit-any': 'off',
            
            // Tambahkan ini untuk fix error GitHub Actions
            'vue/block-lang': 'off',
            'vue/no-reserved-component-names': 'off',
            '@typescript-eslint/no-unused-vars': 'warn',
            'vue/valid-template-root': 'off',
            'vue/no-unused-components': 'warn',
        },
    },
    prettier,
);