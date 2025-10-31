import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import { type BreadcrumbItem } from '@/types';
import { type PropsWithChildren, type ReactNode } from 'react';

interface AppSidebarLayoutProps extends PropsWithChildren {
    header?: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default function AppSidebarLayout({
    children,
    header,
    breadcrumbs = [],
}: AppSidebarLayoutProps) {
    return (
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent variant="sidebar" className="overflow-x-hidden">
                <AppSidebarHeader header={header} breadcrumbs={breadcrumbs} />
                {children}
            </AppContent>
        </AppShell>
    );
}
