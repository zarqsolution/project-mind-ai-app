import { Form, router } from '@inertiajs/react';
import { ArrowDown, ArrowUp, ImagePlus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import ProjectImageController from '@/actions/App/Http/Controllers/ProjectImageController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import type { Project, ProjectImage } from '@/types';

type ProjectImageGalleryProps = {
    project: Project;
};

export function ProjectImageGallery({ project }: ProjectImageGalleryProps) {
    const images = project.images ?? [];
    const [addOpen, setAddOpen] = useState(false);
    const [preview, setPreview] = useState<string | null>(null);
    const [deleting, setDeleting] = useState<ProjectImage | null>(null);

    const moveImage = (from: number, to: number) => {
        if (to < 0 || to >= images.length) {
            return;
        }

        const order = images.map((image) => image.id);
        [order[from], order[to]] = [order[to], order[from]];

        router.put(
            ProjectImageController.reorder.url(project.slug),
            { order },
            { preserveScroll: true },
        );
    };

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between gap-4">
                <div>
                    <h2 className="text-lg font-semibold">Screenshots</h2>
                    <p className="text-sm text-muted-foreground">
                        {images.length === 0
                            ? 'No screenshots yet'
                            : `${images.length} ${images.length === 1 ? 'image' : 'images'}`}
                    </p>
                </div>

                <Dialog
                    open={addOpen}
                    onOpenChange={(open) => {
                        setAddOpen(open);

                        if (!open) {
                            setPreview(null);
                        }
                    }}
                >
                    <DialogTrigger asChild>
                        <Button variant="outline" data-test="add-image-button">
                            <ImagePlus />
                            Add Image
                        </Button>
                    </DialogTrigger>

                    <DialogContent>
                        <DialogTitle>Add screenshot</DialogTitle>

                        <DialogDescription>
                            Upload a screenshot with a short title describing
                            what it shows.
                        </DialogDescription>

                        <Form
                            {...ProjectImageController.store.form(project.slug)}
                            options={{ preserveScroll: true }}
                            onSuccess={() => {
                                setAddOpen(false);
                                setPreview(null);
                            }}
                            resetOnSuccess
                            className="space-y-6"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <div className="grid gap-2">
                                        <Label htmlFor="image-title">
                                            Title
                                        </Label>

                                        <Input
                                            id="image-title"
                                            name="title"
                                            required
                                            placeholder="e.g. Dashboard overview"
                                            data-test="image-title-input"
                                        />

                                        <InputError message={errors.title} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="image-file">
                                            Image
                                        </Label>

                                        {preview && (
                                            <div className="aspect-video w-full overflow-hidden rounded-lg border bg-muted">
                                                <img
                                                    src={preview}
                                                    alt="Preview"
                                                    className="size-full object-cover"
                                                />
                                            </div>
                                        )}

                                        <Input
                                            id="image-file"
                                            name="image"
                                            type="file"
                                            accept="image/*"
                                            required
                                            onChange={(event) => {
                                                const file =
                                                    event.target.files?.[0];

                                                if (file) {
                                                    setPreview(
                                                        URL.createObjectURL(
                                                            file,
                                                        ),
                                                    );
                                                }
                                            }}
                                            data-test="image-file-input"
                                        />

                                        <InputError message={errors.image} />
                                    </div>

                                    <DialogFooter className="gap-2">
                                        <DialogClose asChild>
                                            <Button variant="secondary">
                                                Cancel
                                            </Button>
                                        </DialogClose>

                                        <Button
                                            disabled={processing}
                                            data-test="upload-image-button"
                                        >
                                            {processing && <Spinner />}
                                            Upload
                                        </Button>
                                    </DialogFooter>
                                </>
                            )}
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>

            {images.length === 0 ? (
                <div className="flex flex-col items-center justify-center gap-2 rounded-xl border border-dashed py-12 text-center">
                    <ImagePlus className="size-8 text-muted-foreground/50" />

                    <p className="text-sm text-muted-foreground">
                        Add screenshots so proposals can reference real screens.
                    </p>
                </div>
            ) : (
                <div className="grid gap-6 lg:grid-cols-2">
                    {images.map((image, imageIndex) => (
                        <Card
                            key={image.id}
                            className="gap-0 overflow-hidden py-0"
                        >
                            <div className="aspect-video w-full overflow-hidden bg-muted">
                                <img
                                    src={image.image_url}
                                    alt={image.title}
                                    className="size-full object-cover"
                                />
                            </div>

                            <CardContent className="flex items-center justify-between gap-2 p-4">
                                <p className="truncate font-medium">
                                    {image.title}
                                </p>

                                <div className="flex shrink-0 items-center gap-1">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        disabled={imageIndex === 0}
                                        onClick={() =>
                                            moveImage(
                                                imageIndex,
                                                imageIndex - 1,
                                            )
                                        }
                                        aria-label={`Move ${image.title} up`}
                                    >
                                        <ArrowUp />
                                    </Button>

                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        disabled={
                                            imageIndex === images.length - 1
                                        }
                                        onClick={() =>
                                            moveImage(
                                                imageIndex,
                                                imageIndex + 1,
                                            )
                                        }
                                        aria-label={`Move ${image.title} down`}
                                    >
                                        <ArrowDown />
                                    </Button>

                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        className="text-destructive hover:text-destructive"
                                        onClick={() => setDeleting(image)}
                                        aria-label={`Delete ${image.title}`}
                                        data-test={`delete-image-${image.id}`}
                                    >
                                        <Trash2 />
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            )}

            <Dialog
                open={deleting !== null}
                onOpenChange={(open) => {
                    if (!open) {
                        setDeleting(null);
                    }
                }}
            >
                <DialogContent>
                    <DialogTitle>Delete "{deleting?.title}"?</DialogTitle>

                    <DialogDescription>
                        This will permanently delete the screenshot. This cannot
                        be undone.
                    </DialogDescription>

                    {deleting && (
                        <Form
                            {...ProjectImageController.destroy.form({
                                project: project.slug,
                                image: deleting.id,
                            })}
                            options={{ preserveScroll: true }}
                            onSuccess={() => setDeleting(null)}
                        >
                            {({ processing }) => (
                                <DialogFooter className="gap-2">
                                    <DialogClose asChild>
                                        <Button variant="secondary">
                                            Cancel
                                        </Button>
                                    </DialogClose>

                                    <Button
                                        variant="destructive"
                                        disabled={processing}
                                        asChild
                                    >
                                        <button
                                            type="submit"
                                            data-test="confirm-delete-image-button"
                                        >
                                            Delete Image
                                        </button>
                                    </Button>
                                </DialogFooter>
                            )}
                        </Form>
                    )}
                </DialogContent>
            </Dialog>
        </div>
    );
}
