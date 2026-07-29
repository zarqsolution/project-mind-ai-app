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
import { index as projects } from '@/routes/projects';
import { index as promptTemplates } from '@/routes/prompt-templates';
import { index as proposalWriter } from '@/routes/proposal-writer';

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

const stats = [
    { title: 'Projects', value: 0, icon: FolderGit2, href: projects() },
    { title: 'Images', value: 0, icon: ImageIcon, href: projects() },
    {
        title: 'Prompt Templates',
        value: 0,
        icon: NotebookPen,
        href: promptTemplates(),
    },
    { title: 'Proposals', value: 0, icon: History, href: history() },
];

export default function Dashboard() {
    const { auth } = usePage().props;
    const firstName = auth.user?.name?.split(' ')[0] ?? 'there';

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
                    {stats.map((stat) => (
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
                        <CardContent className="flex flex-1 flex-col items-center justify-center gap-3 py-10 text-center">
                            <div className="flex size-12 items-center justify-center rounded-2xl bg-accent text-accent-foreground">
                                <FolderGit2 className="size-6" />
                            </div>
                            <p className="text-sm text-muted-foreground">
                                No projects yet. Add your first project to start
                                building your portfolio.
                            </p>
                            <Button asChild size="sm">
                                <Link href={projects()} prefetch>
                                    <Plus className="size-4" />
                                    Add Project
                                </Link>
                            </Button>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Proposals</CardTitle>
                            <CardDescription>
                                Previously generated proposals
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="flex flex-1 flex-col items-center justify-center gap-3 py-10 text-center">
                            <div className="flex size-12 items-center justify-center rounded-2xl bg-accent text-accent-foreground">
                                <PenLine className="size-6" />
                            </div>
                            <p className="text-sm text-muted-foreground">
                                No proposals yet. Paste a job description and
                                let AI write the first one.
                            </p>
                            <Button asChild size="sm" variant="outline">
                                <Link href={proposalWriter()} prefetch>
                                    <PenLine className="size-4" />
                                    Write Proposal
                                </Link>
                            </Button>
                        </CardContent>
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
