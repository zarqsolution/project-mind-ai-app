import { Form, Head, Link, usePage } from '@inertiajs/react';
import ProjectController from '@/actions/App/Http/Controllers/ProjectController';
import Heading from '@/components/heading';
import { ProjectFormFields } from '@/components/project-form-fields';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { edit, index, show } from '@/routes/projects';
import type { Project } from '@/types';

type PageProps = {
    project: Project;
};

export default function ProjectsEdit() {
    const { project } = usePage<PageProps>().props;

    return (
        <>
            <Head title={`Edit ${project.title}`} />

            <div className="mx-auto w-full max-w-3xl space-y-6">
                <Heading
                    title="Edit Project"
                    description="Update the project details"
                />

                <Form
                    {...ProjectController.update.form(project.slug)}
                    options={{ preserveScroll: true }}
                    className="space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <ProjectFormFields
                                errors={errors}
                                defaultTitle={project.title}
                                defaultDescription={project.description}
                                currentThumbnailUrl={project.thumbnail_url}
                            />

                            <div className="flex items-center gap-4">
                                <Button
                                    disabled={processing}
                                    data-test="update-project-button"
                                >
                                    {processing && <Spinner />}
                                    Save Changes
                                </Button>

                                <Button asChild variant="ghost">
                                    <Link href={show(project.slug)}>
                                        Cancel
                                    </Link>
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

ProjectsEdit.layout = ({ project }: PageProps) => ({
    breadcrumbs: [
        {
            title: 'Portfolio',
            href: index(),
        },
        {
            title: project.title,
            href: show(project.slug),
        },
        {
            title: 'Edit',
            href: edit(project.slug),
        },
    ],
});
