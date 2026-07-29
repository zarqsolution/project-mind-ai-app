import { Head } from '@inertiajs/react';
import { Sparkles } from 'lucide-react';
import { ModulePlaceholder } from '@/components/module-placeholder';
import { index } from '@/routes/ai-search';

export default function AiSearchIndex() {
    return (
        <>
            <Head title="AI Search" />
            <ModulePlaceholder
                icon={Sparkles}
                title="AI Search"
                description="Search your portfolio semantically using AI embeddings. Coming in Phase 6."
            />
        </>
    );
}

AiSearchIndex.layout = {
    breadcrumbs: [
        {
            title: 'AI Search',
            href: index(),
        },
    ],
};
