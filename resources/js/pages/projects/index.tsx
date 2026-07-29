import { Head } from '@inertiajs/react';
import { FolderGit2 } from 'lucide-react';
import { ModulePlaceholder } from '@/components/module-placeholder';
import { index } from '@/routes/projects';

export default function ProjectsIndex() {
    return (
        <>
            <Head title="Portfolio" />
            <ModulePlaceholder
                icon={FolderGit2}
                title="Portfolio"
                description="Manage all of your software projects in one place. Coming in Phase 2."
            />
        </>
    );
}

ProjectsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Portfolio',
            href: index(),
        },
    ],
};
