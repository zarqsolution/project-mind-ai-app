import { EditorContent, useEditor, useEditorState } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import {
    Bold,
    Code,
    Heading1,
    Heading2,
    Heading3,
    Italic,
    List,
    ListOrdered,
    Minus,
    Quote,
    Redo2,
    SquareCode,
    Strikethrough,
    Undo2,
} from 'lucide-react';
import { useState } from 'react';
import { Separator } from '@/components/ui/separator';
import { Toggle } from '@/components/ui/toggle';
import { cn } from '@/lib/utils';

type RichTextEditorProps = {
    name: string;
    defaultValue?: string;
    className?: string;
};

export function RichTextEditor({
    name,
    defaultValue = '',
    className,
}: RichTextEditorProps) {
    const [html, setHtml] = useState(defaultValue);

    const editor = useEditor({
        extensions: [StarterKit],
        content: defaultValue,
        immediatelyRender: false,
        editorProps: {
            attributes: {
                class: 'rich-text min-h-96 px-4 py-3 outline-none',
            },
        },
        onUpdate: ({ editor }) => setHtml(editor.getHTML()),
    });

    const editorState = useEditorState({
        editor,
        selector: ({ editor }) => {
            if (!editor) {
                return null;
            }

            return {
                isBold: editor.isActive('bold'),
                isItalic: editor.isActive('italic'),
                isStrike: editor.isActive('strike'),
                isCode: editor.isActive('code'),
                isHeading1: editor.isActive('heading', { level: 1 }),
                isHeading2: editor.isActive('heading', { level: 2 }),
                isHeading3: editor.isActive('heading', { level: 3 }),
                isBulletList: editor.isActive('bulletList'),
                isOrderedList: editor.isActive('orderedList'),
                isBlockquote: editor.isActive('blockquote'),
                isCodeBlock: editor.isActive('codeBlock'),
                canUndo: editor.can().undo(),
                canRedo: editor.can().redo(),
            };
        },
    });

    return (
        <div
            className={cn(
                'rounded-md border border-input shadow-xs transition-[color,box-shadow] focus-within:border-ring focus-within:ring-[3px] focus-within:ring-ring/50 dark:bg-input/30',
                className,
            )}
        >
            <input type="hidden" name={name} value={html} />

            <div className="flex flex-wrap items-center gap-0.5 border-b border-input p-1">
                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Bold"
                    pressed={editorState?.isBold ?? false}
                    onPressedChange={() =>
                        editor?.chain().focus().toggleBold().run()
                    }
                >
                    <Bold />
                </Toggle>

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Italic"
                    pressed={editorState?.isItalic ?? false}
                    onPressedChange={() =>
                        editor?.chain().focus().toggleItalic().run()
                    }
                >
                    <Italic />
                </Toggle>

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Strikethrough"
                    pressed={editorState?.isStrike ?? false}
                    onPressedChange={() =>
                        editor?.chain().focus().toggleStrike().run()
                    }
                >
                    <Strikethrough />
                </Toggle>

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Inline code"
                    pressed={editorState?.isCode ?? false}
                    onPressedChange={() =>
                        editor?.chain().focus().toggleCode().run()
                    }
                >
                    <Code />
                </Toggle>

                <Separator
                    orientation="vertical"
                    className="mx-1 data-[orientation=vertical]:h-6"
                />

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Heading 1"
                    pressed={editorState?.isHeading1 ?? false}
                    onPressedChange={() =>
                        editor
                            ?.chain()
                            .focus()
                            .toggleHeading({ level: 1 })
                            .run()
                    }
                >
                    <Heading1 />
                </Toggle>

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Heading 2"
                    pressed={editorState?.isHeading2 ?? false}
                    onPressedChange={() =>
                        editor
                            ?.chain()
                            .focus()
                            .toggleHeading({ level: 2 })
                            .run()
                    }
                >
                    <Heading2 />
                </Toggle>

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Heading 3"
                    pressed={editorState?.isHeading3 ?? false}
                    onPressedChange={() =>
                        editor
                            ?.chain()
                            .focus()
                            .toggleHeading({ level: 3 })
                            .run()
                    }
                >
                    <Heading3 />
                </Toggle>

                <Separator
                    orientation="vertical"
                    className="mx-1 data-[orientation=vertical]:h-6"
                />

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Bullet list"
                    pressed={editorState?.isBulletList ?? false}
                    onPressedChange={() =>
                        editor?.chain().focus().toggleBulletList().run()
                    }
                >
                    <List />
                </Toggle>

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Ordered list"
                    pressed={editorState?.isOrderedList ?? false}
                    onPressedChange={() =>
                        editor?.chain().focus().toggleOrderedList().run()
                    }
                >
                    <ListOrdered />
                </Toggle>

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Blockquote"
                    pressed={editorState?.isBlockquote ?? false}
                    onPressedChange={() =>
                        editor?.chain().focus().toggleBlockquote().run()
                    }
                >
                    <Quote />
                </Toggle>

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Code block"
                    pressed={editorState?.isCodeBlock ?? false}
                    onPressedChange={() =>
                        editor?.chain().focus().toggleCodeBlock().run()
                    }
                >
                    <SquareCode />
                </Toggle>

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Horizontal rule"
                    pressed={false}
                    onPressedChange={() =>
                        editor?.chain().focus().setHorizontalRule().run()
                    }
                >
                    <Minus />
                </Toggle>

                <Separator
                    orientation="vertical"
                    className="mx-1 data-[orientation=vertical]:h-6"
                />

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Undo"
                    pressed={false}
                    disabled={!editorState?.canUndo}
                    onPressedChange={() => editor?.chain().focus().undo().run()}
                >
                    <Undo2 />
                </Toggle>

                <Toggle
                    type="button"
                    size="sm"
                    aria-label="Redo"
                    pressed={false}
                    disabled={!editorState?.canRedo}
                    onPressedChange={() => editor?.chain().focus().redo().run()}
                >
                    <Redo2 />
                </Toggle>
            </div>

            <EditorContent editor={editor} />
        </div>
    );
}
