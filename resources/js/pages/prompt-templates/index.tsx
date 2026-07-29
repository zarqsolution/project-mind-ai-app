import { Form, Head, router, usePage } from '@inertiajs/react';
import { NotebookPen, Plus, Star, Trash2 } from 'lucide-react';
import { useState } from 'react';
import PromptTemplateController from '@/actions/App/Http/Controllers/PromptTemplateController';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { index } from '@/routes/prompt-templates';
import type { PromptTemplate } from '@/types';

type PageProps = {
    templates: PromptTemplate[];
};

type TemplateFieldsProps = {
    template?: PromptTemplate;
    errors: Record<string, string>;
};

function TemplateFields({ template, errors }: TemplateFieldsProps) {
    const [isDefault, setIsDefault] = useState(template?.is_default ?? false);

    return (
        <>
            <input
                type="hidden"
                name="is_default"
                value={isDefault ? '1' : '0'}
            />

            <div className="grid gap-2">
                <Label htmlFor="template-name">Name</Label>

                <Input
                    id="template-name"
                    name="name"
                    required
                    defaultValue={template?.name}
                    placeholder="e.g. Upwork proposal"
                    data-test="template-name-input"
                />

                <InputError message={errors.name} />
            </div>

            <div className="grid gap-2">
                <Label htmlFor="template-prompt">Prompt</Label>

                <Textarea
                    id="template-prompt"
                    name="prompt"
                    required
                    defaultValue={template?.prompt}
                    rows={12}
                    placeholder="Write the instructions the AI should follow when generating a proposal…"
                    data-test="template-prompt-input"
                />

                <InputError message={errors.prompt} />
            </div>

            <div className="flex items-center gap-2">
                <Checkbox
                    id="template-default"
                    checked={isDefault}
                    disabled={template?.is_default}
                    onCheckedChange={(checked) =>
                        setIsDefault(checked === true)
                    }
                    data-test="template-default-checkbox"
                />

                <Label
                    htmlFor="template-default"
                    className="font-normal text-muted-foreground"
                >
                    {template?.is_default
                        ? 'This is the default template. Mark another template as default to change it.'
                        : 'Use as the default template for proposals'}
                </Label>
            </div>

            <InputError message={errors.is_default} />
        </>
    );
}

export default function PromptTemplatesIndex() {
    const { templates } = usePage<PageProps>().props;
    const [creating, setCreating] = useState(false);
    const [editing, setEditing] = useState<PromptTemplate | null>(null);
    const [deleting, setDeleting] = useState<PromptTemplate | null>(null);

    const makeDefault = (template: PromptTemplate) => {
        router.put(
            PromptTemplateController.update.url(template.id),
            {
                name: template.name,
                prompt: template.prompt,
                is_default: '1',
            },
            { preserveScroll: true },
        );
    };

    return (
        <>
            <Head title="Prompt Templates" />

            <div className="mx-auto w-full max-w-3xl space-y-6">
                <div className="flex items-center justify-between gap-4">
                    <div className="space-y-1">
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Prompt Templates
                        </h1>

                        <p className="text-sm text-muted-foreground">
                            Reusable instructions for the AI Proposal Writer.
                            The default template is used automatically.
                        </p>
                    </div>

                    <Button
                        onClick={() => setCreating(true)}
                        data-test="new-template-button"
                    >
                        <Plus />
                        New Template
                    </Button>
                </div>

                {templates.length === 0 ? (
                    <div className="flex flex-col items-center justify-center gap-2 rounded-xl border border-dashed py-12 text-center">
                        <NotebookPen className="size-8 text-muted-foreground/50" />

                        <p className="text-sm text-muted-foreground">
                            No prompt templates yet. Create one to power the
                            Proposal Writer.
                        </p>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {templates.map((template) => (
                            <Card key={template.id}>
                                <CardContent className="space-y-3">
                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                        <div className="flex items-center gap-2">
                                            <h2 className="font-medium">
                                                {template.name}
                                            </h2>

                                            {template.is_default && (
                                                <Badge variant="secondary">
                                                    <Star />
                                                    Default
                                                </Badge>
                                            )}
                                        </div>

                                        <div className="flex items-center gap-1">
                                            {!template.is_default && (
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() =>
                                                        makeDefault(template)
                                                    }
                                                    data-test={`make-default-${template.id}`}
                                                >
                                                    <Star />
                                                    Make Default
                                                </Button>
                                            )}

                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={() =>
                                                    setEditing(template)
                                                }
                                                data-test={`edit-template-${template.id}`}
                                            >
                                                Edit
                                            </Button>

                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                className="text-destructive hover:text-destructive"
                                                onClick={() =>
                                                    setDeleting(template)
                                                }
                                                aria-label={`Delete ${template.name}`}
                                                data-test={`delete-template-${template.id}`}
                                            >
                                                <Trash2 />
                                            </Button>
                                        </div>
                                    </div>

                                    <p className="line-clamp-3 text-sm whitespace-pre-line text-muted-foreground">
                                        {template.prompt}
                                    </p>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>

            <Dialog open={creating} onOpenChange={setCreating}>
                <DialogContent>
                    <DialogTitle>New prompt template</DialogTitle>

                    <DialogDescription>
                        Write reusable instructions for generating proposals.
                    </DialogDescription>

                    <Form
                        {...PromptTemplateController.store.form()}
                        options={{ preserveScroll: true }}
                        onSuccess={() => setCreating(false)}
                        resetOnSuccess
                        className="space-y-6"
                    >
                        {({ processing, errors }) => (
                            <>
                                <TemplateFields errors={errors} />

                                <DialogFooter className="gap-2">
                                    <DialogClose asChild>
                                        <Button variant="secondary">
                                            Cancel
                                        </Button>
                                    </DialogClose>

                                    <Button
                                        disabled={processing}
                                        data-test="save-template-button"
                                    >
                                        {processing && <Spinner />}
                                        Create Template
                                    </Button>
                                </DialogFooter>
                            </>
                        )}
                    </Form>
                </DialogContent>
            </Dialog>

            <Dialog
                open={editing !== null}
                onOpenChange={(open) => {
                    if (!open) {
                        setEditing(null);
                    }
                }}
            >
                <DialogContent>
                    <DialogTitle>Edit "{editing?.name}"</DialogTitle>

                    <DialogDescription>
                        Update the template name or prompt instructions.
                    </DialogDescription>

                    {editing && (
                        <Form
                            {...PromptTemplateController.update.form(
                                editing.id,
                            )}
                            options={{ preserveScroll: true }}
                            onSuccess={() => setEditing(null)}
                            className="space-y-6"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <TemplateFields
                                        template={editing}
                                        errors={errors}
                                    />

                                    <DialogFooter className="gap-2">
                                        <DialogClose asChild>
                                            <Button variant="secondary">
                                                Cancel
                                            </Button>
                                        </DialogClose>

                                        <Button
                                            disabled={processing}
                                            data-test="update-template-button"
                                        >
                                            {processing && <Spinner />}
                                            Save Changes
                                        </Button>
                                    </DialogFooter>
                                </>
                            )}
                        </Form>
                    )}
                </DialogContent>
            </Dialog>

            <Dialog
                open={deleting !== null}
                onOpenChange={(open) => {
                    if (!open) {
                        setDeleting(null);
                    }
                }}
            >
                <DialogContent>
                    <DialogTitle>Delete "{deleting?.name}"?</DialogTitle>

                    <DialogDescription>
                        This will permanently delete the prompt template. This
                        cannot be undone.
                    </DialogDescription>

                    {deleting && (
                        <Form
                            {...PromptTemplateController.destroy.form(
                                deleting.id,
                            )}
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
                                            data-test="confirm-delete-template-button"
                                        >
                                            Delete Template
                                        </button>
                                    </Button>
                                </DialogFooter>
                            )}
                        </Form>
                    )}
                </DialogContent>
            </Dialog>
        </>
    );
}

PromptTemplatesIndex.layout = {
    breadcrumbs: [
        {
            title: 'Prompt Templates',
            href: index(),
        },
    ],
};
