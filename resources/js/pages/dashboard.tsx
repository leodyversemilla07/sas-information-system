import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { ActivityIcon, ChartBarIcon, UsersIcon } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="relative flex aspect-video items-center justify-center overflow-hidden rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia variant="icon">
                                    <UsersIcon className="size-8" />
                                </EmptyMedia>
                                <EmptyTitle>No Users</EmptyTitle>
                                <EmptyDescription>
                                    No user data available yet.
                                </EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    </div>
                    <div className="relative flex aspect-video items-center justify-center overflow-hidden rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia variant="icon">
                                    <ChartBarIcon className="size-8" />
                                </EmptyMedia>
                                <EmptyTitle>No Statistics</EmptyTitle>
                                <EmptyDescription>
                                    Statistics will appear here.
                                </EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    </div>
                    <div className="relative flex aspect-video items-center justify-center overflow-hidden rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia variant="icon">
                                    <ActivityIcon className="size-8" />
                                </EmptyMedia>
                                <EmptyTitle>No Activity</EmptyTitle>
                                <EmptyDescription>
                                    Recent activity will show here.
                                </EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    </div>
                </div>
                <div className="relative flex min-h-[100vh] flex-1 items-center justify-center overflow-hidden rounded-xl border border-sidebar-border/70 bg-card md:min-h-min dark:border-sidebar-border">
                    <Empty>
                        <EmptyHeader>
                            <EmptyMedia variant="icon">
                                <ActivityIcon className="size-12" />
                            </EmptyMedia>
                            <EmptyTitle>No Dashboard Data</EmptyTitle>
                            <EmptyDescription>
                                Your dashboard content will appear here once data is available.
                            </EmptyDescription>
                        </EmptyHeader>
                    </Empty>
                </div>
            </div>
        </AppLayout>
    );
}
