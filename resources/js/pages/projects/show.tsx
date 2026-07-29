import { Form, Head, Link, usePage } from '@inertiajs/react';
import { ImageIcon, Pencil, Trash2 } from 'lucide-react';
import ProjectController from '@/actions/App/Http/Controllers/ProjectController';
import { ProjectImageGallery } from '@/components/project-image-gallery';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { edit, index, show } from '@/routes/projects';
import type { Project } from '@/types';

type PageProps = {
    project: Project;
};

export default function ProjectsShow() {
    const { project } = usePage<PageProps>().props;

    return (
        <>
            <Head title={project.title} />

            <div className="mx-auto w-full max-w-4xl space-y-6">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div className="space-y-1">
                        <h1 className="text-2xl font-semibold tracking-tight">
                            {project.title}
                        </h1>

                        <p className="text-sm text-muted-foreground">
                            Last updated{' '}
                            {new Date(project.updated_at).toLocaleDateString()}
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Button asChild variant="outline">
                            <Link href={edit(project.slug)}>
                                <Pencil />
                                Edit
                            </Link>
                        </Button>

                        <Dialog>
                            <DialogTrigger asChild>
                                <Button
                                    variant="destructive"
                                    data-test="delete-project-button"
                                >
                                    <Trash2 />
                                    Delete
                                </Button>
                            </DialogTrigger>

                            <DialogContent>
                                <DialogTitle>
                                    Delete "{project.title}"?
                                </DialogTitle>

                                <DialogDescription>
                                    This will permanently delete the project,
                                    its thumbnail, and all related data. This
                                    cannot be undone.
                                </DialogDescription>

                                <Form
                                    {...ProjectController.destroy.form(
                                        project.slug,
                                    )}
                                >
                                    {({ processing }) => (
                                        <DialogFooter className="gap-2">
                                            <DialogClose asChild>
                                                <Button variant="secondary">
                                                    Cancel
                                                </Button>
                                            </DialogClose>

                                            <Button
                                                variant="destructive"
                                                disabled={processing}
                                                asChild
                                            >
                                                <button
                                                    type="submit"
                                                    data-test="confirm-delete-project-button"
                                                >
                                                    Delete Project
                                                </button>
                                            </Button>
                                        </DialogFooter>
                                    )}
                                </Form>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>

                <div className="overflow-hidden rounded-xl border bg-muted">
                    {project.thumbnail_url ? (
                        <img
                            src={project.thumbnail_url}
                            alt={project.title}
                            className="max-h-[480px] w-full object-cover"
                        />
                    ) : (
                        <div className="flex aspect-video w-full items-center justify-center">
                            <div className="flex flex-col items-center gap-2 text-muted-foreground">
                                <ImageIcon className="size-10" />
                                <span className="text-sm">No thumbnail</span>
                            </div>
                        </div>
                    )}
                </div>

                <div className="rounded-xl border bg-card p-6">
                    <div className="text-sm leading-relaxed whitespace-pre-wrap">
                        {project.description}
                    </div>
                </div>

                <ProjectImageGallery project={project} />
            </div>
        </>
    );
}

ProjectsShow.layout = ({ project }: PageProps) => ({
    breadcrumbs: [
        {
            title: 'Portfolio',
            href: index(),
        },
        {
            title: project.title,
            href: show(project.slug),
        },
    ],
});
