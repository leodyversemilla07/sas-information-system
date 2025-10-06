import { Link, usePage } from '@inertiajs/react';
import { Search, User, Menu, Home, Users, Megaphone, Calendar, Lightbulb, FileText, MessageSquare, ChevronDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { useState } from 'react';
import AppLogoIcon from '@/components/app-logo-icon';

export default function Header() {
    const { url } = usePage();
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

    const navItems = [
        { name: 'Home', href: '/usg', icon: Home, active: url === '/usg' },
        { name: 'Officers', href: '/usg/officers', icon: Users, active: url.startsWith('/usg/officers') },
        { name: 'Announcements', href: '/usg/announcements', icon: Megaphone, active: url.startsWith('/usg/announcements') },
        { name: 'Calendar', href: '/usg/calendar', icon: Calendar, active: url.startsWith('/usg/calendar') },
        {
            name: 'Projects',
            href: '/usg/projects',
            icon: Lightbulb,
            active: url.startsWith('/usg/projects'),
            dropdown: [
                { name: 'Ongoing Projects', href: '/usg/projects/ongoing' },
                { name: 'Completed Projects', href: '/usg/projects/completed' },
                { name: 'Sustainability', href: '/usg/projects/sustainability' },
                { name: 'Leadership Programs', href: '/usg/projects/leadership' }
            ]
        },
        {
            name: 'Transparency',
            href: '/usg/transparency',
            icon: FileText,
            active: url.startsWith('/usg/transparency'),
            dropdown: [
                { name: 'Resolutions', href: '/usg/resolutions' },
                { name: 'Reports', href: '/usg/transparency/reports' },
                { name: 'Budget', href: '/usg/transparency/budget' },
                { name: 'Policies', href: '/usg/transparency/policies' }
            ]
        },
        { name: 'Feedback', href: '/usg/feedback', icon: MessageSquare, active: url.startsWith('/usg/feedback') }
    ];

    const NavLink = ({ item, mobile = false }: { item: typeof navItems[0], mobile?: boolean }) => {
        const baseClasses = mobile
            ? "flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors hover:bg-gray-50"
            : "flex items-center gap-2 px-3 py-2 text-sm font-medium transition-all duration-200 hover:text-white hover:bg-white/10 rounded-md";

        if (item.dropdown) {
            return (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <button className={`${baseClasses} ${item.active ? 'text-white bg-white/20' : 'text-gray-700'}`}>
                            {item.name}
                            <ChevronDown size={14} />
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="start" className="w-48">
                        {item.dropdown.map((subItem) => (
                            <DropdownMenuItem key={subItem.href} asChild>
                                <Link href={subItem.href} className="cursor-pointer">
                                    {subItem.name}
                                </Link>
                            </DropdownMenuItem>
                        ))}
                    </DropdownMenuContent>
                </DropdownMenu>
            );
        }

        return (
            <Link
                href={item.href}
                className={`${baseClasses} ${item.active ? 'text-white bg-white/20' : 'text-gray-700'}`}
            >
                {item.name}
            </Link>
        );
    };

    return (
        <header className="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200">
            {/* Tier 1: Identity Bar */}
            <div className="bg-white border-b border-gray-100">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between h-16">
                        {/* Left: University Branding */}
                        <div className="flex items-center gap-4">
                            <div className="flex items-center gap-3">
                                {/* MSU and USG Logos */}
                                <div className="flex items-center gap-2">
                                    <AppLogoIcon className="w-10 h-10 fill-green-700" />
                                    <img
                                        src="/usg-logo.png"
                                        alt="USG Logo"
                                        className="w-10 h-10 rounded-full object-cover"
                                    />
                                </div>
                                <div className="flex items-center gap-2 text-sm">
                                    <span className="font-semibold text-gray-900">Mindoro State University — Bongabong Campus</span>
                                    <span className="text-gray-400">|</span>
                                    <span className="font-medium text-green-700">University Student Government</span>
                                </div>
                            </div>
                        </div>

                        {/* Right: Search and Profile */}
                        <div className="flex items-center gap-3">
                            <Button variant="ghost" size="sm" className="text-gray-600 hover:text-gray-900">
                                <Search size={18} />
                            </Button>
                            <Button variant="ghost" size="sm" className="text-gray-600 hover:text-gray-900">
                                <User size={18} />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            {/* Tier 2: Navigation Bar */}
            <div className="bg-gradient-to-r from-green-700 to-green-800">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <nav className="flex items-center justify-between h-14">
                        {/* Desktop Navigation */}
                        <div className="hidden md:flex items-center space-x-1">
                            {navItems.map((item) => (
                                <NavLink key={item.name} item={item} />
                            ))}
                        </div>

                        {/* Mobile Navigation */}
                        <div className="md:hidden">
                            <Sheet open={isMobileMenuOpen} onOpenChange={setIsMobileMenuOpen}>
                                <SheetTrigger asChild>
                                    <Button variant="ghost" size="sm" className="text-white hover:bg-white/10">
                                        <Menu size={20} />
                                    </Button>
                                </SheetTrigger>
                                <SheetContent side="left" className="w-80">
                                    <div className="flex flex-col gap-2 mt-6">
                                        {navItems.map((item) => (
                                            <div key={item.name}>
                                                <NavLink item={item} mobile />
                                                {item.dropdown && (
                                                    <div className="ml-8 mt-1 space-y-1">
                                                        {item.dropdown.map((subItem) => (
                                                            <Link
                                                                key={subItem.href}
                                                                href={subItem.href}
                                                                className="block px-4 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md"
                                                                onClick={() => setIsMobileMenuOpen(false)}
                                                            >
                                                                {subItem.name}
                                                            </Link>
                                                        ))}
                                                    </div>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                </SheetContent>
                            </Sheet>
                        </div>

                        {/* Mobile Title */}
                        <div className="md:hidden">
                            <span className="text-white font-medium">USG Portal</span>
                        </div>

                        {/* Desktop Spacer */}
                        <div className="hidden md:block w-8" />
                    </nav>
                </div>
            </div>
        </header>
    );
}
