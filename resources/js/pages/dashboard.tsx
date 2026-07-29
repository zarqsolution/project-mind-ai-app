import { Head, Link, usePage } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import {
    ArrowRight,
    FolderGit2,
    History,
    ImageIcon,
    NotebookPen,
    PenLine,
    Plus,
    Sparkles,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as aiSearch } from '@/routes/ai-search';
import { index as history } from '@/routes/history';
import { index as projects, show as showProject } from '@/routes/projects';
import { index as promptTemplates } from '@/routes/prompt-templates';
import { index as proposalWriter } from '@/routes/proposal-writer';
import type { Project, PromptHistoryEntry } from '@/types';

type PageProps = {
    stats: {
        projects: number;
        images: number;
        promptTemplates: number;
        proposals: number;
    };
    recentProjects: Project[];
    recentProposals: PromptHistoryEntry[];
};

function excerpt(text: string, length = 100): string {
    const plain = text
        .replace(/<[^>]*>/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();

    return plain.length > length ? `${plain.slice(0, length)}…` : plain;
}

type QuickAction = {
    title: string;
    description: string;
    href: ReturnType<typeof dashboard>;
    icon: LucideIcon;
    primary?: boolean;
};

const quickActions: QuickAction[] = [
    {
        title: 'Write Proposal',
        description: 'Paste a job description and generate a tailored proposal',
        href: proposalWriter(),
        icon: PenLine,
        primary: true,
    },
    {
        title: 'AI Search',
        description: 'Find the most relevant projects semantically',
        href: aiSearch(),
        icon: Sparkles,
    },
    {
        title: 'Add Project',
        description: 'Document a new project in under five minutes',
        href: projects(),
        icon: Plus,
    },
];

export default function Dashboard() {
    const { auth, stats, recentProjects, recentProposals } =
        usePage<PageProps>().props;
    const firstName = auth.user?.name?.split(' ')[0] ?? 'there';

    const statCards = [
        {
            title: 'Projects',
            value: stats.projects,
            icon: FolderGit2,
            href: projects(),
        },
        {
            title: 'Images',
            value: stats.images,
            icon: ImageIcon,
            href: projects(),
        },
        {
            title: 'Prompt Templates',
            value: stats.promptTemplates,
            icon: NotebookPen,
            href: promptTemplates(),
        },
        {
            title: 'Proposals',
            value: stats.proposals,
            icon: History,
            href: history(),
        },
    ];

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Welcome back, {firstName}
                    </h1>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Your portfolio command center — manage projects, search
                        with AI, and write winning proposals.
                    </p>
                </div>

                <div className="grid gap-4 md:grid-cols-3">
                    {quickActions.map((action) => (
                        <Link key={action.title} href={action.href} prefetch>
                            <Card
                                className={
                                    action.primary
                                        ? 'group h-full border-primary/40 bg-primary text-primary-foreground transition-all hover:shadow-lg hover:shadow-primary/25'
                                        : 'group h-full transition-all hover:border-primary/40 hover:shadow-md'
                                }
                            >
                                <CardHeader>
                                    <div className="flex items-center justify-between">
                                        <div
                                            className={
                                                action.primary
                                                    ? 'flex size-10 items-center justify-center rounded-xl bg-primary-foreground/15'
                                                    : 'flex size-10 items-center justify-center rounded-xl bg-accent text-accent-foreground'
                                            }
                                        >
                                            <action.icon className="size-5" />
                                        </div>
                                        <ArrowRight className="size-4 opacity-0 transition-all group-hover:translate-x-0.5 group-hover:opacity-100" />
                                    </div>
                                    <CardTitle className="pt-2">
                                        {action.title}
                                    </CardTitle>
                                    <CardDescription
                                        className={
                                            action.primary
                                                ? 'text-primary-foreground/75'
                                                : undefined
                                        }
                                    >
                                        {action.description}
                                    </CardDescription>
                                </CardHeader>
                            </Card>
                        </Link>
                    ))}
                </div>

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {statCards.map((stat) => (
                        <Link key={stat.title} href={stat.href} prefetch>
                            <Card className="transition-all hover:border-primary/40">
                                <CardContent className="flex items-center gap-4">
                                    <div className="flex size-10 items-center justify-center rounded-xl bg-accent text-accent-foreground">
                                        <stat.icon className="size-5" />
                                    </div>
                                    <div>
                                        <p className="text-2xl font-semibold tabular-nums">
                                            {stat.value}
                                        </p>
                                        <p className="text-xs text-muted-foreground">
                                            {stat.title}
                                        </p>
                                    </div>
                                </CardContent>
                            </Card>
                        </Link>
                    ))}
                </div>

                <div className="grid flex-1 gap-4 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Projects</CardTitle>
                            <CardDescription>
                                Your latest portfolio entries
                            </CardDescription>
                        </CardHeader>
                        {recentProjects.length === 0 ? (
                            <CardContent className="flex flex-1 flex-col items-center justify-center gap-3 py-10 text-center">
                                <div className="flex size-12 items-center justify-center rounded-2xl bg-accent text-accent-foreground">
                                    <FolderGit2 className="size-6" />
                                </div>
                                <p className="text-sm text-muted-foreground">
                                    No projects yet. Add your first project to
                                    start building your portfolio.
                                </p>
                                <Button asChild size="sm">
                                    <Link href={projects()} prefetch>
                                        <Plus className="size-4" />
                                        Add Project
                                    </Link>
                                </Button>
                            </CardContent>
                        ) : (
                            <CardContent className="space-y-1">
                                {recentProjects.map((project) => (
                                    <Link
                                        key={project.id}
                                        href={showProject(project.slug)}
                                        prefetch
                                        className="flex items-center gap-3 rounded-lg p-2 transition-colors hover:bg-accent"
                                        data-test={`recent-project-${project.id}`}
                                    >
                                        <div className="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-muted">
                                            {project.thumbnail_url ? (
                                                <img
                                                    src={project.thumbnail_url}
                                                    alt={project.title}
                                                    className="size-full object-cover"
                                                />
                                            ) : (
                                                <ImageIcon className="size-4 text-muted-foreground/50" />
                                            )}
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <p className="truncate text-sm font-medium">
                                                {project.title}
                                            </p>
                                            <p className="text-xs text-muted-foreground">
                                                Updated{' '}
                                                {new Date(
                                                    project.updated_at,
                                                ).toLocaleDateString()}
                                            </p>
                                        </div>
                                        <ArrowRight className="size-4 shrink-0 text-muted-foreground" />
                                    </Link>
                                ))}
                            </CardContent>
                        )}
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Proposals</CardTitle>
                            <CardDescription>
                                Previously generated proposals
                            </CardDescription>
                        </CardHeader>
                        {recentProposals.length === 0 ? (
                            <CardContent className="flex flex-1 flex-col items-center justify-center gap-3 py-10 text-center">
                                <div className="flex size-12 items-center justify-center rounded-2xl bg-accent text-accent-foreground">
                                    <PenLine className="size-6" />
                                </div>
                                <p className="text-sm text-muted-foreground">
                                    No proposals yet. Paste a job description
                                    and let AI write the first one.
                                </p>
                                <Button asChild size="sm" variant="outline">
                                    <Link href={proposalWriter()} prefetch>
                                        <PenLine className="size-4" />
                                        Write Proposal
                                    </Link>
                                </Button>
                            </CardContent>
                        ) : (
                            <CardContent className="space-y-1">
                                {recentProposals.map((proposal) => (
                                    <Link
                                        key={proposal.id}
                                        href={history()}
                                        prefetch
                                        className="flex items-center gap-3 rounded-lg p-2 transition-colors hover:bg-accent"
                                        data-test={`recent-proposal-${proposal.id}`}
                                    >
                                        <div className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-accent text-accent-foreground">
                                            <PenLine className="size-4" />
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <p className="truncate text-sm font-medium">
                                                {excerpt(
                                                    proposal.job_description,
                                                )}
                                            </p>
                                            <p className="text-xs text-muted-foreground">
                                                {proposal.created_at
                                                    ? new Date(
                                                          proposal.created_at,
                                                      ).toLocaleString(
                                                          undefined,
                                                          {
                                                              dateStyle:
                                                                  'medium',
                                                              timeStyle:
                                                                  'short',
                                                          },
                                                      )
                                                    : '—'}
                                            </p>
                                        </div>
                                        <ArrowRight className="size-4 shrink-0 text-muted-foreground" />
                                    </Link>
                                ))}
                            </CardContent>
                        )}
                    </Card>
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
