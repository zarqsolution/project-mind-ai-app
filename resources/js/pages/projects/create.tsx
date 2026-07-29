import { Form, Head, Link, usePage } from '@inertiajs/react';
import ProjectController from '@/actions/App/Http/Controllers/ProjectController';
import Heading from '@/components/heading';
import { ProjectFormFields } from '@/components/project-form-fields';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { create, index } from '@/routes/projects';

type PageProps = {
    descriptionTemplate: string;
};

export default function ProjectsCreate() {
    const { descriptionTemplate } = usePage<PageProps>().props;

    return (
        <>
            <Head title="Add Project" />

            <div className="mx-auto w-full max-w-3xl space-y-6">
                <Heading
                    title="Add Project"
                    description="Document a project for your portfolio"
                />

                <Form
                    {...ProjectController.store.form()}
                    options={{ preserveScroll: true }}
                    className="space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <ProjectFormFields
                                errors={errors}
                                defaultDescription={descriptionTemplate}
                            />

                            <div className="flex items-center gap-4">
                                <Button
                                    disabled={processing}
                                    data-test="create-project-button"
                                >
                                    {processing && <Spinner />}
                                    Create Project
                                </Button>

                                <Button asChild variant="ghost">
                                    <Link href={index()}>Cancel</Link>
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

ProjectsCreate.layout = {
    breadcrumbs: [
        {
            title: 'Portfolio',
            href: index(),
        },
        {
            title: 'Add Project',
            href: create(),
        },
    ],
};
