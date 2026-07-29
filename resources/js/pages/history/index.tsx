import { Head } from '@inertiajs/react';
import { History } from 'lucide-react';
import { ModulePlaceholder } from '@/components/module-placeholder';
import { index } from '@/routes/history';

export default function HistoryIndex() {
    return (
        <>
            <Head title="History" />
            <ModulePlaceholder
                icon={History}
                title="Prompt History"
                description="Review, reuse, and manage previously generated proposals. Coming in Phase 9."
            />
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
