import { Form, Head, Link, usePage } from '@inertiajs/react';
import { Eye, History, RotateCcw, Trash2 } from 'lucide-react';
import { useState } from 'react';
import PromptHistoryController from '@/actions/App/Http/Controllers/PromptHistoryController';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import { index } from '@/routes/history';
import { index as proposalWriter } from '@/routes/proposal-writer';
import type { Paginated, PromptHistoryEntry } from '@/types';

type PageProps = {
    history: Paginated<PromptHistoryEntry>;
};

function excerpt(text: string, length = 160): string {
    const plain = text.replace(/\s+/g, ' ').trim();

    return plain.length > length ? `${plain.slice(0, length)}…` : plain;
}

function formatDate(date: string | null): string {
    if (!date) {
        return '—';
    }

    return new Date(date).toLocaleString(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
}

export default function HistoryIndex() {
    const { history } = usePage<PageProps>().props;
    const [viewing, setViewing] = useState<PromptHistoryEntry | null>(null);
    const [deleting, setDeleting] = useState<PromptHistoryEntry | null>(null);

    return (
        <>
            <Head title="History" />

            <div className="mx-auto w-full max-w-3xl space-y-6">
                <div className="space-y-1">
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Prompt History
                    </h1>

                    <p className="text-sm text-muted-foreground">
                        Every generated proposal is saved here automatically.
                        Review, reuse, or delete past generations.
                    </p>
                </div>

                {history.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center gap-2 rounded-xl border border-dashed py-12 text-center">
                        <History className="size-8 text-muted-foreground/50" />

                        <p className="text-sm text-muted-foreground">
                            No history yet. Generate a proposal to see it here.
                        </p>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {history.data.map((entry) => (
                            <Card key={entry.id}>
                                <CardContent className="space-y-3">
                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                        <p className="text-xs text-muted-foreground">
                                            {formatDate(entry.created_at)}
                                        </p>

                                        <div className="flex items-center gap-1">
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={() =>
                                                    setViewing(entry)
                                                }
                                                data-test={`view-history-${entry.id}`}
                                            >
                                                <Eye />
                                                View
                                            </Button>

                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                asChild
                                            >
                                                <Link
                                                    href={proposalWriter({
                                                        query: {
                                                            job_description:
                                                                entry.job_description,
                                                        },
                                                    })}
                                                    data-test={`reuse-history-${entry.id}`}
                                                >
                                                    <RotateCcw />
                                                    Reuse
                                                </Link>
                                            </Button>

                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                className="text-destructive hover:text-destructive"
                                                onClick={() =>
                                                    setDeleting(entry)
                                                }
                                                aria-label="Delete history entry"
                                                data-test={`delete-history-${entry.id}`}
                                            >
                                                <Trash2 />
                                            </Button>
                                        </div>
                                    </div>

                                    <div className="space-y-1">
                                        <p className="text-xs font-medium tracking-wide text-muted-foreground uppercase">
                                            Job Description
                                        </p>

                                        <p className="text-sm">
                                            {excerpt(entry.job_description)}
                                        </p>
                                    </div>

                                    <div className="space-y-1">
                                        <p className="text-xs font-medium tracking-wide text-muted-foreground uppercase">
                                            Proposal
                                        </p>

                                        <p className="line-clamp-2 text-sm text-muted-foreground">
                                            {excerpt(entry.generated_proposal)}
                                        </p>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}

                {history.last_page > 1 && (
                    <div className="flex items-center justify-between gap-4">
                        <p className="text-sm text-muted-foreground">
                            Showing {history.from}–{history.to} of{' '}
                            {history.total}
                        </p>

                        <div className="flex items-center gap-1">
                            {history.links.map((link, linkIndex) =>
                                link.url ? (
                                    <Button
                                        key={linkIndex}
                                        asChild
                                        variant={
                                            link.active ? 'default' : 'outline'
                                        }
                                        size="sm"
                                    >
                                        <Link
                                            href={link.url}
                                            preserveScroll
                                            dangerouslySetInnerHTML={{
                                                __html: link.label,
                                            }}
                                        />
                                    </Button>
                                ) : (
                                    <Button
                                        key={linkIndex}
                                        variant="ghost"
                                        size="sm"
                                        disabled
                                        className={cn('text-muted-foreground')}
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                ),
                            )}
                        </div>
                    </div>
                )}
            </div>

            <Dialog
                open={viewing !== null}
                onOpenChange={(open) => {
                    if (!open) {
                        setViewing(null);
                    }
                }}
            >
                <DialogContent className="max-h-[85vh] overflow-y-auto sm:max-w-2xl">
                    <DialogTitle>
                        Generated {formatDate(viewing?.created_at ?? null)}
                    </DialogTitle>

                    <DialogDescription>
                        The job description, the exact prompt sent to the AI,
                        and the generated proposal.
                    </DialogDescription>

                    {viewing && (
                        <div className="space-y-4">
                            <div className="grid gap-2">
                                <Label>Job Description</Label>

                                <p className="rounded-md border bg-muted/40 p-3 text-sm whitespace-pre-wrap">
                                    {viewing.job_description}
                                </p>
                            </div>

                            <div className="grid gap-2">
                                <Label>Used Prompt</Label>

                                <p className="rounded-md border bg-muted/40 p-3 text-sm whitespace-pre-wrap">
                                    {viewing.used_prompt}
                                </p>
                            </div>

                            <div className="grid gap-2">
                                <Label>Generated Proposal</Label>

                                <p
                                    className="rounded-md border bg-muted/40 p-3 text-sm whitespace-pre-wrap"
                                    data-test="history-proposal"
                                >
                                    {viewing.generated_proposal}
                                </p>
                            </div>
                        </div>
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
                    <DialogTitle>Delete this history entry?</DialogTitle>

                    <DialogDescription>
                        This will permanently delete the saved proposal and
                        prompt. This cannot be undone.
                    </DialogDescription>

                    {deleting && (
                        <Form
                            {...PromptHistoryController.destroy.form(
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
                                            data-test="confirm-delete-history-button"
                                        >
                                            Delete Entry
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

HistoryIndex.layout = {
    breadcrumbs: [
        {
            title: 'History',
            href: index(),
        },
    ],
};
