import { Link } from '@inertiajs/react';
import {
    FolderGit2,
    History,
    LayoutGrid,
    NotebookPen,
    PenLine,
    Settings,
    Sparkles,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as aiSearch } from '@/routes/ai-search';
import { index as history } from '@/routes/history';
import { edit as profileEdit } from '@/routes/profile';
import { index as projects } from '@/routes/projects';
import { index as promptTemplates } from '@/routes/prompt-templates';
import { index as proposalWriter } from '@/routes/proposal-writer';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Portfolio',
        href: projects(),
        icon: FolderGit2,
    },
    {
        title: 'AI Search',
        href: aiSearch(),
        icon: Sparkles,
    },
    {
        title: 'Proposal Writer',
        href: proposalWriter(),
        icon: PenLine,
    },
    {
        title: 'Prompt Templates',
        href: promptTemplates(),
        icon: NotebookPen,
    },
    {
        title: 'History',
        href: history(),
        icon: History,
    },
    {
        title: 'Settings',
        href: profileEdit(),
        icon: Settings,
    },
];

const footerNavItems: NavItem[] = [];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
