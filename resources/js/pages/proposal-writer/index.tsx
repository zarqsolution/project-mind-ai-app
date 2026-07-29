import { Head } from '@inertiajs/react';
import { PenLine } from 'lucide-react';
import { ModulePlaceholder } from '@/components/module-placeholder';
import { index } from '@/routes/proposal-writer';

export default function ProposalWriterIndex() {
    return (
        <>
            <Head title="Proposal Writer" />
            <ModulePlaceholder
                icon={PenLine}
                title="Proposal Writer"
                description="Generate personalized Upwork proposals from your portfolio. Coming in Phase 8."
            />
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
