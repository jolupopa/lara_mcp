import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { type BreadcrumbItem } from '@/types';
import { type ReactNode } from 'react';

interface AppLayoutProps {
    children: ReactNode;
    header?: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default ({ children, header, breadcrumbs, ...props }: AppLayoutProps) => (
    <AppLayoutTemplate header={header} breadcrumbs={breadcrumbs} {...props}>
        {children}
    </AppLayoutTemplate>
);
