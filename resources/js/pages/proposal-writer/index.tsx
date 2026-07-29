import { Form, Head, usePage } from '@inertiajs/react';
import { Check, Copy, PenLine, Sparkles } from 'lucide-react';
import { useState } from 'react';
import ProposalController from '@/actions/App/Http/Controllers/ProposalController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { index } from '@/routes/proposal-writer';

type PageProps = {
    proposal: string | null;
};

export default function ProposalWriterIndex() {
    const { proposal } = usePage<PageProps>().props;
    const [copied, setCopied] = useState(false);

    const copyProposal = async () => {
        if (!proposal) {
            return;
        }

        await navigator.clipboard.writeText(proposal);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    return (
        <>
            <Head title="Proposal Writer" />

            <div className="space-y-6">
                <div className="space-y-1">
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Proposal Writer
                    </h1>

                    <p className="text-sm text-muted-foreground">
                        Paste a job description. The AI finds your most relevant
                        projects and writes a personalized proposal using your
                        default prompt template.
                    </p>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Form
                        {...ProposalController.store.form()}
                        options={{ preserveScroll: true }}
                        className="space-y-4"
                    >
                        {({ processing, errors }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor="job-description">
                                        Job Description
                                    </Label>

                                    <Textarea
                                        id="job-description"
                                        name="job_description"
                                        required
                                        rows={18}
                                        placeholder="Paste the Upwork job description here…"
                                        className="min-h-96"
                                        data-test="job-description-input"
                                    />

                                    <InputError
                                        message={errors.job_description}
                                    />
                                </div>

                                <Button
                                    disabled={processing}
                                    data-test="generate-proposal-button"
                                >
                                    {processing ? <Spinner /> : <Sparkles />}
                                    {processing
                                        ? 'Generating…'
                                        : 'Generate Proposal'}
                                </Button>
                            </>
                        )}
                    </Form>

                    <div className="space-y-4">
                        <div className="flex items-center justify-between gap-2">
                            <Label>Generated Proposal</Label>

                            {proposal && (
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={copyProposal}
                                    data-test="copy-proposal-button"
                                >
                                    {copied ? <Check /> : <Copy />}
                                    {copied ? 'Copied' : 'Copy'}
                                </Button>
                            )}
                        </div>

                        {proposal ? (
                            <Card>
                                <CardContent>
                                    <p
                                        className="text-sm whitespace-pre-wrap"
                                        data-test="generated-proposal"
                                    >
                                        {proposal}
                                    </p>
                                </CardContent>
                            </Card>
                        ) : (
                            <div className="flex min-h-96 flex-col items-center justify-center gap-2 rounded-xl border border-dashed py-12 text-center">
                                <PenLine className="size-8 text-muted-foreground/50" />

                                <p className="max-w-xs text-sm text-muted-foreground">
                                    Your generated proposal will appear here.
                                </p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

ProposalWriterIndex.layout = {
    breadcrumbs: [
        {
            title: 'Proposal Writer',
            href: index(),
        },
    ],
};
