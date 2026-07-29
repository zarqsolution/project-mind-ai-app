import { ImageIcon } from 'lucide-react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { RichTextEditor } from '@/components/rich-text-editor';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type ProjectFormFieldsProps = {
    errors: Record<string, string>;
    defaultTitle?: string;
    defaultDescription?: string;
    currentThumbnailUrl?: string | null;
};

export function ProjectFormFields({
    errors,
    defaultTitle = '',
    defaultDescription = '',
    currentThumbnailUrl = null,
}: ProjectFormFieldsProps) {
    const [thumbnailPreview, setThumbnailPreview] = useState<string | null>(
        currentThumbnailUrl,
    );

    return (
        <>
            <div className="grid gap-2">
                <Label htmlFor="title">Title</Label>

                <Input
                    id="title"
                    name="title"
                    defaultValue={defaultTitle}
                    required
                    autoFocus
                    placeholder="e.g. Real-time Chat Application"
                    data-test="project-title-input"
                />

                <InputError message={errors.title} />
            </div>

            <div className="grid gap-2">
                <Label htmlFor="thumbnail">Thumbnail</Label>

                <div className="flex flex-col gap-4 sm:flex-row sm:items-start">
                    <div className="flex aspect-video w-full max-w-md items-center justify-center overflow-hidden rounded-lg border bg-muted">
                        {thumbnailPreview ? (
                            <img
                                src={thumbnailPreview}
                                alt="Thumbnail preview"
                                className="size-full object-cover"
                            />
                        ) : (
                            <div className="flex flex-col items-center gap-2 text-muted-foreground">
                                <ImageIcon className="size-8" />
                                <span className="text-xs">No thumbnail</span>
                            </div>
                        )}
                    </div>

                    <div className="grid flex-1 gap-2">
                        <Input
                            id="thumbnail"
                            name="thumbnail"
                            type="file"
                            accept="image/*"
                            onChange={(event) => {
                                const file = event.target.files?.[0];

                                if (file) {
                                    setThumbnailPreview(
                                        URL.createObjectURL(file),
                                    );
                                }
                            }}
                            data-test="project-thumbnail-input"
                        />

                        <p className="text-xs text-muted-foreground">
                            PNG, JPG or WebP, up to 4MB.
                        </p>
                    </div>
                </div>

                <InputError message={errors.thumbnail} />
            </div>

            <div className="grid gap-2">
                <Label>Description</Label>

                <RichTextEditor
                    name="description"
                    defaultValue={defaultDescription}
                />

                <p className="text-xs text-muted-foreground">
                    Follow the template sections for the best AI search results.
                </p>

                <InputError message={errors.description} />
            </div>
        </>
    );
}
