import { Head } from '@inertiajs/react';
import { NotebookPen } from 'lucide-react';
import { ModulePlaceholder } from '@/components/module-placeholder';
import { index } from '@/routes/prompt-templates';

export default function PromptTemplatesIndex() {
    return (
        <>
            <Head title="Prompt Templates" />
            <ModulePlaceholder
                icon={NotebookPen}
                title="Prompt Templates"
                description="Create and manage reusable proposal prompt templates. Coming in Phase 7."
            />
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
