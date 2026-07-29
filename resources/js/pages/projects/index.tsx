import { Head, Link, router, usePage } from '@inertiajs/react';
import { FolderGit2, ImageIcon, Plus, Search } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';
import { create, index, show } from '@/routes/projects';
import type { Paginated, Project } from '@/types';

type PageProps = {
    projects: Paginated<Project>;
    filters: {
        search: string;
    };
};

function excerpt(text: string, length = 140): string {
    const plain = text
        .replace(/[#*_`>[\]()!-]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();

    return plain.length > length ? `${plain.slice(0, length)}…` : plain;
}

export default function ProjectsIndex() {
    const { projects, filters } = usePage<PageProps>().props;
    const [search, setSearch] = useState(filters.search);
    const debounce = useRef<ReturnType<typeof setTimeout>>(null);
    const isFirstRender = useRef(true);

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;

            return;
        }

        debounce.current = setTimeout(() => {
            router.get(index().url, search ? { search } : {}, {
                preserveState: true,
                replace: true,
            });
        }, 300);

        return () => {
            if (debounce.current) {
                clearTimeout(debounce.current);
            }
        };
    }, [search]);

    return (
        <>
            <Head title="Portfolio" />

            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <Heading
                        title="Portfolio"
                        description="All of your software projects in one place"
                    />

                    <Button asChild>
                        <Link href={create()}>
                            <Plus />
                            Add Project
                        </Link>
                    </Button>
                </div>

                <div className="relative max-w-sm">
                    <Search className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                    <Input
                        value={search}
                        onChange={(event) => setSearch(event.target.value)}
                        placeholder="Search projects by title..."
                        className="pl-9"
                        data-test="project-search-input"
                    />
                </div>

                {projects.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed py-20 text-center">
                        <div className="flex size-12 items-center justify-center rounded-full bg-muted">
                            <FolderGit2 className="size-6 text-muted-foreground" />
                        </div>

                        <div className="space-y-1">
                            <p className="font-medium">
                                {filters.search
                                    ? 'No projects match your search'
                                    : 'No projects yet'}
                            </p>

                            <p className="text-sm text-muted-foreground">
                                {filters.search
                                    ? 'Try a different search term.'
                                    : 'Add your first project to start building your portfolio.'}
                            </p>
                        </div>

                        {!filters.search && (
                            <Button asChild variant="outline" className="mt-2">
                                <Link href={create()}>
                                    <Plus />
                                    Add Project
                                </Link>
                            </Button>
                        )}
                    </div>
                ) : (
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {projects.data.map((project) => (
                            <Link
                                key={project.id}
                                href={show(project.slug)}
                                className="group"
                            >
                                <Card className="h-full gap-0 overflow-hidden py-0 transition-colors hover:border-primary/40">
                                    <div className="aspect-video w-full overflow-hidden bg-muted">
                                        {project.thumbnail_url ? (
                                            <img
                                                src={project.thumbnail_url}
                                                alt={project.title}
                                                className="size-full object-cover transition-transform duration-300 group-hover:scale-105"
                                            />
                                        ) : (
                                            <div className="flex size-full items-center justify-center">
                                                <ImageIcon className="size-8 text-muted-foreground/50" />
                                            </div>
                                        )}
                                    </div>

                                    <CardContent className="space-y-2 p-4">
                                        <h3 className="font-semibold group-hover:text-primary">
                                            {project.title}
                                        </h3>

                                        <p className="line-clamp-2 text-sm text-muted-foreground">
                                            {excerpt(project.description)}
                                        </p>

                                        <p className="text-xs text-muted-foreground">
                                            Updated{' '}
                                            {new Date(
                                                project.updated_at,
                                            ).toLocaleDateString()}
                                        </p>
                                    </CardContent>
                                </Card>
                            </Link>
                        ))}
                    </div>
                )}

                {projects.last_page > 1 && (
                    <div className="flex items-center justify-between gap-4">
                        <p className="text-sm text-muted-foreground">
                            Showing {projects.from}–{projects.to} of{' '}
                            {projects.total}
                        </p>

                        <div className="flex items-center gap-1">
                            {projects.links.map((link, linkIndex) =>
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
