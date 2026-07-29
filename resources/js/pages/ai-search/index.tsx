import { Head, Link, router, usePage } from '@inertiajs/react';
import { ImageIcon, Search, Sparkles } from 'lucide-react';
import { useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import { index } from '@/routes/ai-search';
import { show } from '@/routes/projects';
import type { Project } from '@/types';

type SearchResult = {
    project: Project;
    score: number;
    reason: string;
};

type PageProps = {
    query: string;
    results: SearchResult[] | null;
};

const exampleQueries = [
    'Show me logistics projects',
    'Projects with OCR',
    'Laravel APIs',
    'Healthcare',
    'Stripe integration',
    'Marketplace',
    'Redis queues',
];

export default function AiSearchIndex() {
    const { query, results } = usePage<PageProps>().props;
    const [term, setTerm] = useState(query);
    const [searching, setSearching] = useState(false);

    const search = (value: string) => {
        const trimmed = value.trim();

        if (!trimmed) {
            return;
        }

        setTerm(trimmed);

        router.get(
            index().url,
            { query: trimmed },
            {
                preserveState: true,
                onStart: () => setSearching(true),
                onFinish: () => setSearching(false),
            },
        );
    };

    return (
        <>
            <Head title="AI Search" />

            <div className="mx-auto w-full max-w-3xl space-y-6">
                <div className="space-y-1">
                    <h1 className="text-2xl font-semibold tracking-tight">
                        AI Search
                    </h1>

                    <p className="text-sm text-muted-foreground">
                        Search your portfolio semantically. Describe what you
                        are looking for in plain language.
                    </p>
                </div>

                <form
                    className="flex gap-2"
                    onSubmit={(event) => {
                        event.preventDefault();
                        search(term);
                    }}
                >
                    <Input
                        value={term}
                        onChange={(event) => setTerm(event.target.value)}
                        placeholder="e.g. Projects with real-time tracking"
                        autoFocus
                        data-test="ai-search-input"
                    />

                    <Button
                        type="submit"
                        disabled={searching || !term.trim()}
                        data-test="ai-search-button"
                    >
                        {searching ? <Spinner /> : <Search />}
                        Search
                    </Button>
                </form>

                <div className="flex flex-wrap gap-2">
                    {exampleQueries.map((example) => (
                        <button
                            key={example}
                            type="button"
                            onClick={() => search(example)}
                            className="rounded-full border px-3 py-1 text-xs text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                        >
                            {example}
                        </button>
                    ))}
                </div>

                {results !== null && (
                    <div className="space-y-4">
                        <h2 className="text-sm font-medium text-muted-foreground">
                            {results.length > 0
                                ? `Best matches for "${query}"`
                                : `No matches for "${query}". Generate embeddings for your projects first.`}
                        </h2>

                        {results.map(({ project, score, reason }) => (
                            <Card key={project.id}>
                                <CardContent className="flex flex-col gap-4 sm:flex-row sm:items-center">
                                    <div className="flex aspect-video w-full shrink-0 items-center justify-center overflow-hidden rounded-lg border bg-muted sm:w-44">
                                        {project.thumbnail_url ? (
                                            <img
                                                src={project.thumbnail_url}
                                                alt={project.title}
                                                className="size-full object-cover"
                                            />
                                        ) : (
                                            <ImageIcon className="size-6 text-muted-foreground" />
                                        )}
                                    </div>

                                    <div className="flex-1 space-y-1">
                                        <div className="flex flex-wrap items-center gap-2">
                                            <h3 className="font-medium">
                                                {project.title}
                                            </h3>

                                            <Badge variant="secondary">
                                                <Sparkles />
                                                {Math.round(score * 100)}% match
                                            </Badge>
                                        </div>

                                        <p className="text-sm text-muted-foreground">
                                            {reason}
                                        </p>
                                    </div>

                                    <Button asChild variant="outline">
                                        <Link href={show(project.slug)}>
                                            Open Project
                                        </Link>
                                    </Button>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
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
