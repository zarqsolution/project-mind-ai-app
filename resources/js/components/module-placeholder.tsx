import type { LucideIcon } from 'lucide-react';

type ModulePlaceholderProps = {
    icon: LucideIcon;
    title: string;
    description: string;
};

export function ModulePlaceholder({
    icon: Icon,
    title,
    description,
}: ModulePlaceholderProps) {
    return (
        <div className="flex h-full flex-1 flex-col p-4">
            <div className="flex flex-1 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-sidebar-border/70 dark:border-sidebar-border">
                <div className="flex size-14 items-center justify-center rounded-2xl bg-accent text-accent-foreground">
                    <Icon className="size-7" />
                </div>
                <div className="text-center">
                    <h2 className="text-lg font-semibold">{title}</h2>
                    <p className="mt-1 max-w-sm text-sm text-muted-foreground">
                        {description}
                    </p>
                </div>
            </div>
        </div>
    );
}
