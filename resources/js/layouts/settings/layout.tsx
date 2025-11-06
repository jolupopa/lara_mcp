import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { cn, isSameUrl, resolveUrl } from '@/lib/utils';
import { edit as userAppearanceEditRoute } from '@/routes/appearance';
import { edit as userProfileEditRoute } from '@/routes/profile';
import { show as userTwoFactorShowRoute } from '@/routes/two-factor';
import { edit as userPasswordEditRoute } from '@/routes/user-password';
import { edit as adminProfileEditRoute } from '@/routes/admin/profile';
import { edit as adminPasswordEditRoute } from '@/routes/admin/password';
import { edit as adminAppearanceEditRoute } from '@/routes/admin/appearance'; // Assuming this route exists or will be created
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

export default function SettingsLayout({ children }: PropsWithChildren) {
    const { auth } = usePage<SharedData>().props;
    const guard = auth.guard;

    const sidebarNavItems: NavItem[] = [
        {
            title: 'Profile',
            href: guard === 'admin' ? adminProfileEditRoute() : userProfileEditRoute(),
            icon: null,
        },
        {
            title: 'Password',
            href: guard === 'admin' ? adminPasswordEditRoute() : userPasswordEditRoute(),
            icon: null,
        },
        ...(guard === 'web'
            ? [
                  {
                      title: 'Two-Factor Auth',
                      href: userTwoFactorShowRoute(),
                      icon: null,
                  },
              ]
            : []),
        {
            title: 'Appearance',
            href: guard === 'admin' ? adminAppearanceEditRoute() : userAppearanceEditRoute(),
            icon: null,
        },
    ];

    // When server-side rendering, we only render the layout on the client...
    if (typeof window === 'undefined') {
        return null;
    }

    const currentPath = window.location.pathname;

    return (
        <div className="px-4 py-6">
            <Heading
                title="Settings"
                description="Manage your profile and account settings"
            />

            <div className="flex flex-col lg:flex-row lg:space-x-12">
                <aside className="w-full max-w-xl lg:w-48">
                    <nav className="flex flex-col space-y-1 space-x-0">
                        {sidebarNavItems.map((item, index) => (
                            <Button
                                key={`${resolveUrl(item.href)}-${index}`}
                                size="sm"
                                variant="ghost"
                                asChild
                                className={cn('w-full justify-start', {
                                    'bg-muted': isSameUrl(
                                        currentPath,
                                        item.href,
                                    ),
                                })}
                            >
                                <Link href={item.href}>
                                    {item.icon && (
                                        <item.icon className="h-4 w-4" />
                                    )}
                                    {item.title}
                                </Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <Separator className="my-6 lg:hidden" />

                <div className="flex-1 md:max-w-2xl">
                    <section className="max-w-xl space-y-12">
                        {children}
                    </section>
                </div>
            </div>
        </div>
    );
}
