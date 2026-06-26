<?php

use Livewire\Attributes\Modelable;
use Livewire\Component;

new class extends Component {
    #[Modelable]
    public $value = '';

    public string $placeholder = 'Escribe aqui tu contenido en Markdown...';
}; ?>

@assets
    <link rel="stylesheet" href="{{ asset('vendor/toastui/toastui-editor.min.css') }}">
    <script src="{{ asset('vendor/toastui/toastui-editor-all.min.js') }}"></script>
@endassets

<div
    x-data="{
        value: @entangle('value'),
        editor: null,
        isSyncingFromLivewire: false,
        isFocused: false,
        init() {
            this.waitForToastEditor(() => this.bootEditor());
        },
        waitForToastEditor(callback, attempts = 0) {
            if (window.toastui?.Editor) {
                callback();

                return;
            }

            if (attempts > 60) {
                console.error('TOAST UI Editor asset was not loaded.');

                return;
            }

            requestAnimationFrame(() => this.waitForToastEditor(callback, attempts + 1));
        },
        bootEditor() {
            if (this.editor || !this.$refs.editor) {
                return;
            }

            this.editor = new window.toastui.Editor({
                el: this.$refs.editor,
                height: '560px',
                initialEditType: 'markdown',
                previewStyle: 'vertical',
                initialValue: this.normalizedValue(this.value),
                placeholder: @js($placeholder),
                usageStatistics: false,
                toolbarItems: [
                    ['heading', 'bold', 'italic', 'strike'],
                    ['hr', 'quote'],
                    ['ul', 'ol', 'task'],
                    ['table', 'link'],
                    ['code', 'codeblock'],
                ],
            });

            this.editor.on('change', () => {
                this.syncValueToLivewire();
            });

            this.$watch('value', (newValue) => {
                this.syncValueFromLivewire(newValue);
            });
        },
        normalizedValue(value) {
            return value ?? '';
        },
        syncValueToLivewire() {
            if (this.isSyncingFromLivewire || !this.editor) {
                return;
            }

            const content = this.normalizedValue(this.editor.getMarkdown());

            if (this.normalizedValue(this.value) !== content) {
                this.value = content;
            }
        },
        syncValueFromLivewire(newValue) {
            if (!this.editor || this.isFocused) {
                return;
            }

            const nextContent = this.normalizedValue(newValue);

            if (this.normalizedValue(this.editor.getMarkdown()) === nextContent) {
                return;
            }

            this.isSyncingFromLivewire = true;

            try {
                this.editor.setMarkdown(nextContent, false);
            } catch (error) {
                console.error('TOAST UI Editor sync error:', error);
            }

            this.$nextTick(() => {
                this.isSyncingFromLivewire = false;
            });
        },
        destroy() {
            if (this.editor) {
                this.syncValueToLivewire();
                this.editor.destroy();
                this.editor = null;
            }
        },
    }"
    x-on:focusin="isFocused = true"
    x-on:focusout="isFocused = false; syncValueToLivewire()"
    class="w-full"
>
    <div wire:ignore>
        <div x-ref="editor" class="markdown-editor-surface"></div>
    </div>

    <style>
        /* ==================== LIGHT MODE STYLES ==================== */
        .markdown-editor-surface .toastui-editor-defaultUI {
            border-color: var(--color-frost-border) !important;
        }

        .markdown-editor-surface .toastui-editor-defaultUI-toolbar,
        .markdown-editor-surface .toastui-editor-md-tab-container,
        .markdown-editor-surface .toastui-editor-mode-switch {
            background-color: var(--color-zinc-50) !important;
            border-color: var(--color-frost-border) !important;
        }

        .markdown-editor-surface .toastui-editor-main,
        .markdown-editor-surface .toastui-editor-main-container,
        .markdown-editor-surface .toastui-editor-md-container,
        .markdown-editor-surface .toastui-editor-ww-container,
        .markdown-editor-surface .toastui-editor {
            background-color: #ffffff !important;
            color: var(--color-frost-dark) !important;
        }

        .markdown-editor-surface .toastui-editor-contents,
        .markdown-editor-surface .ProseMirror,
        .markdown-editor-surface .toastui-editor-md-preview,
        .markdown-editor-surface .toastui-editor-md-mode {
            background-color: #ffffff !important;
            color: var(--color-frost-dark) !important;
        }

        /* Light Mode Links */
        .markdown-editor-surface .ProseMirror a,
        .markdown-editor-surface .toastui-editor-contents a {
            color: #2563eb !important;
            text-decoration: underline !important;
        }

        /* Light Mode Code & Code Blocks */
        .markdown-editor-surface .toastui-editor-md-code,
        .markdown-editor-surface .toastui-editor-md-code-block,
        .markdown-editor-surface .ProseMirror pre,
        .markdown-editor-surface .ProseMirror code,
        .markdown-editor-surface .toastui-editor-contents pre,
        .markdown-editor-surface .toastui-editor-contents code {
            background-color: var(--color-zinc-100) !important;
            border-color: var(--color-frost-border) !important;
            color: var(--color-zinc-800) !important;
        }


        /* ==================== DARK MODE STYLES ==================== */
        /* Borders & Framework */
        .dark .markdown-editor-surface .toastui-editor-defaultUI {
            border-color: var(--color-zinc-700) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-md-splitter {
            background-color: var(--color-zinc-700) !important;
        }

        /* Toolbar and Tabs Backgrounds */
        .dark .markdown-editor-surface .toastui-editor-defaultUI-toolbar,
        .dark .markdown-editor-surface .toastui-editor-md-tab-container,
        .dark .markdown-editor-surface .toastui-editor-mode-switch {
            background-color: var(--color-zinc-800) !important;
            border-color: var(--color-zinc-700) !important;
        }

        /* Main Editing Areas Backgrounds & Base Text */
        .dark .markdown-editor-surface .toastui-editor-main,
        .dark .markdown-editor-surface .toastui-editor-main-container,
        .dark .markdown-editor-surface .toastui-editor-md-container,
        .dark .markdown-editor-surface .toastui-editor-ww-container,
        .dark .markdown-editor-surface .toastui-editor {
            background-color: var(--color-zinc-900) !important;
            color: var(--color-zinc-200) !important;
        }

        /* Contents, ProseMirror Editor and Preview Backgrounds */
        .dark .markdown-editor-surface .toastui-editor-contents,
        .dark .markdown-editor-surface .ProseMirror,
        .dark .markdown-editor-surface .toastui-editor-md-preview,
        .dark .markdown-editor-surface .toastui-editor-md-mode {
            background-color: var(--color-zinc-900) !important;
            color: var(--color-zinc-200) !important;
        }

        /* Specific WYSIWYG Content Elements */
        .dark .markdown-editor-surface .ProseMirror p,
        .dark .markdown-editor-surface .ProseMirror h1,
        .dark .markdown-editor-surface .ProseMirror h2,
        .dark .markdown-editor-surface .ProseMirror h3,
        .dark .markdown-editor-surface .ProseMirror h4,
        .dark .markdown-editor-surface .ProseMirror h5,
        .dark .markdown-editor-surface .ProseMirror h6,
        .dark .markdown-editor-surface .ProseMirror li,
        .dark .markdown-editor-surface .ProseMirror table,
        .dark .markdown-editor-surface .toastui-editor-contents p,
        .dark .markdown-editor-surface .toastui-editor-contents h1,
        .dark .markdown-editor-surface .toastui-editor-contents h2,
        .dark .markdown-editor-surface .toastui-editor-contents h3,
        .dark .markdown-editor-surface .toastui-editor-contents h4,
        .dark .markdown-editor-surface .toastui-editor-contents h5,
        .dark .markdown-editor-surface .toastui-editor-contents h6,
        .dark .markdown-editor-surface .toastui-editor-contents li,
        .dark .markdown-editor-surface .toastui-editor-contents table {
            color: var(--color-zinc-200) !important;
        }

        /* Dark Mode Links */
        .dark .markdown-editor-surface .ProseMirror a,
        .dark .markdown-editor-surface .toastui-editor-contents a {
            color: #60a5fa !important;
            text-decoration: underline !important;
        }

        /* Dark Mode Code & Code Blocks */
        .dark .markdown-editor-surface .toastui-editor-md-code,
        .dark .markdown-editor-surface .toastui-editor-md-code-block,
        .dark .markdown-editor-surface .ProseMirror pre,
        .dark .markdown-editor-surface .ProseMirror code,
        .dark .markdown-editor-surface .toastui-editor-contents pre,
        .dark .markdown-editor-surface .toastui-editor-contents code {
            background-color: var(--color-zinc-800) !important;
            border-color: var(--color-zinc-700) !important;
            color: var(--color-zinc-200) !important;
        }

        /* Toolbar Buttons & Icons Visibility */
        .dark .markdown-editor-surface .toastui-editor-defaultUI-toolbar button {
            color: var(--color-zinc-400) !important;
            border-color: transparent !important;
        }

        .dark .markdown-editor-surface .toastui-editor-defaultUI-toolbar button:hover {
            color: var(--color-zinc-100) !important;
            background-color: var(--color-zinc-700) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-defaultUI-toolbar .toastui-editor-toolbar-icons {
            filter: invert(1) brightness(2) !important;
        }

        /* Tab Controls (Markdown vs Preview) */
        .dark .markdown-editor-surface .toastui-editor-tabs {
            background-color: var(--color-zinc-800) !important;
            border-bottom: 1px solid var(--color-zinc-700) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-tabs .tab-item,
        .dark .markdown-editor-surface .toastui-editor-tabs button {
            background-color: var(--color-zinc-800) !important;
            color: var(--color-zinc-400) !important;
            border-color: transparent !important;
        }

        .dark .markdown-editor-surface .toastui-editor-tabs .tab-item:hover,
        .dark .markdown-editor-surface .toastui-editor-tabs button:hover {
            color: var(--color-zinc-200) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-tabs .te-tab-active,
        .dark .markdown-editor-surface .toastui-editor-tabs .active,
        .dark .markdown-editor-surface .toastui-editor-tabs .tab-item-active,
        .dark .markdown-editor-surface .toastui-editor-tabs button.active {
            background-color: var(--color-zinc-900) !important;
            color: var(--color-zinc-100) !important;
            border-color: var(--color-zinc-700) !important;
            border-bottom-color: var(--color-zinc-900) !important;
        }

        /* Mode Switch (Markdown vs WYSIWYG) at the bottom */
        .dark .markdown-editor-surface .toastui-editor-mode-switch {
            background-color: var(--color-zinc-800) !important;
            border-top: 1px solid var(--color-zinc-700) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-mode-switch .tab-item,
        .dark .markdown-editor-surface .toastui-editor-mode-switch button {
            background-color: var(--color-zinc-800) !important;
            color: var(--color-zinc-400) !important;
            border-color: transparent !important;
        }

        .dark .markdown-editor-surface .toastui-editor-mode-switch .tab-item:hover,
        .dark .markdown-editor-surface .toastui-editor-mode-switch button:hover {
            color: var(--color-zinc-200) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-mode-switch .te-switch-active,
        .dark .markdown-editor-surface .toastui-editor-mode-switch .active,
        .dark .markdown-editor-surface .toastui-editor-mode-switch button.active {
            background-color: var(--color-zinc-900) !important;
            color: var(--color-zinc-100) !important;
            border: 1px solid var(--color-zinc-700) !important;
            border-bottom: none !important;
        }

        /* Dark Mode Popups/Dialogs (e.g. Add Link, Add Image, Add Table) */
        .dark .markdown-editor-surface .toastui-editor-popup {
            background-color: var(--color-zinc-800) !important;
            border-color: var(--color-zinc-700) !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-popup-body {
            background-color: var(--color-zinc-800) !important;
            color: var(--color-zinc-200) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-popup .toastui-editor-popup-body {
            color: var(--color-zinc-200) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-popup label {
            color: var(--color-zinc-300) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-popup input {
            background-color: var(--color-zinc-900) !important;
            border: 1px solid var(--color-zinc-700) !important;
            color: var(--color-zinc-100) !important;
            border-radius: 4px !important;
            padding: 4px 8px !important;
        }

        .dark .markdown-editor-surface .toastui-editor-popup input:focus {
            border-color: var(--color-zinc-500) !important;
            outline: none !important;
        }

        /* Buttons inside Popups/Dialogs */
        .dark .markdown-editor-surface .toastui-editor-button {
            background-color: var(--color-zinc-700) !important;
            border: 1px solid var(--color-zinc-600) !important;
            color: var(--color-zinc-200) !important;
            border-radius: 4px !important;
        }

        .dark .markdown-editor-surface .toastui-editor-button:hover {
            background-color: var(--color-zinc-600) !important;
            color: var(--color-zinc-100) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-ok-button {
            background-color: var(--color-zinc-200) !important;
            border: 1px solid var(--color-zinc-300) !important;
            color: var(--color-zinc-900) !important;
            font-weight: 500 !important;
        }

        .dark .markdown-editor-surface .toastui-editor-ok-button:hover {
            background-color: #ffffff !important;
            color: var(--color-zinc-950) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-close-button {
            background-color: var(--color-zinc-700) !important;
            border: 1px solid var(--color-zinc-600) !important;
            color: var(--color-zinc-300) !important;
        }

        .dark .markdown-editor-surface .toastui-editor-close-button:hover {
            background-color: var(--color-zinc-600) !important;
            color: var(--color-zinc-100) !important;
        }

        /* Placeholder styles */
        .dark .markdown-editor-surface .toastui-editor-placeholder,
        .dark .markdown-editor-surface .ProseMirror.placeholder:before,
        .dark .markdown-editor-surface .ProseMirror.toastui-editor-contents-placeholder:before {
            color: var(--color-zinc-500) !important;
        }
    </style>
</div>
