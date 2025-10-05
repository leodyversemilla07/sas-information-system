import { Link } from '@inertiajs/react';
import { Head } from '@inertiajs/react';
import { home } from '@/routes';

export default function UsgLanding() {
    return (
        <>
            <Head title="USG Transparency Portal" />

            <div className="min-h-screen bg-[#F7FAFC] text-[#0b0b0b] p-6 lg:p-12">
                <header className="mx-auto max-w-6xl">
                    <nav className="flex items-center justify-between py-4">
                        <div className="text-lg font-semibold">USG Transparency Portal</div>
                        <div className="flex items-center gap-3">
                            <Link href={home()} className="text-sm text-gray-700 hover:underline">
                                Home
                            </Link>
                            <a
                                href="/usg/announcements"
                                className="rounded-sm bg-[#1b1b18] px-4 py-1.5 text-sm text-white hover:opacity-95"
                            >
                                Announcements
                            </a>
                        </div>
                    </nav>
                </header>

                <main className="mx-auto mt-8 max-w-6xl">
                    <section className="grid gap-8 lg:grid-cols-2 lg:items-center">
                        <div>
                            <h1 className="mb-4 text-3xl font-bold leading-tight">
                                USG Transparency Portal
                            </h1>
                            <p className="mb-6 text-gray-600">
                                Bringing openness to student governance. Browse announcements,
                                view resolutions, check the public calendar, and submit feedback.
                            </p>

                            <div className="flex flex-wrap gap-3">
                                <a
                                    href="/usg/announcements"
                                    className="inline-block rounded bg-[#F53003] px-5 py-2 text-sm font-medium text-white"
                                >
                                    Explore Announcements
                                </a>
                                <a
                                    href="/usg/calendar"
                                    className="inline-block rounded border border-gray-300 px-5 py-2 text-sm text-gray-700"
                                >
                                    View Calendar
                                </a>
                            </div>
                        </div>

                        <div className="rounded-lg bg-white p-6 shadow">
                            <h3 className="mb-3 text-lg font-medium">Key features</h3>
                            <ul className="space-y-3 text-sm text-gray-700">
                                <li>
                                    <strong>Announcements</strong> — Official notices and
                                    priority updates from USG.
                                </li>
                                <li>
                                    <strong>Events / Calendar</strong> — Campus events synced to
                                    the public calendar.
                                </li>
                                <li>
                                    <strong>Feedback</strong> — Submit concerns or suggestions
                                    directly to USG.
                                </li>
                                <li>
                                    <strong>Resolutions</strong> — Browse policies and published
                                    resolutions with PDF viewer.
                                </li>
                            </ul>
                        </div>
                    </section>

                    <section className="mt-12 grid gap-6 lg:grid-cols-3">
                        <div className="rounded-lg bg-white p-6 shadow">
                            <h4 className="mb-2 font-medium">About</h4>
                            <p className="text-sm text-gray-600">
                                The USG Transparency Portal offers students and stakeholders
                                an easy way to review governance activity and participate in
                                campus life.
                            </p>
                        </div>

                        <div className="rounded-lg bg-white p-6 shadow">
                            <h4 className="mb-2 font-medium">Contact</h4>
                            <p className="text-sm text-gray-600">usg@minsubongabong.edu.ph</p>
                            <p className="text-sm text-gray-600">Office: Student Center, 2nd Floor</p>
                        </div>

                        <div className="rounded-lg bg-white p-6 shadow">
                            <h4 className="mb-2 font-medium">Accessibility</h4>
                            <p className="text-sm text-gray-600">
                                This page follows the site's look and supports mobile,
                                tablet, and desktop layouts.
                            </p>
                        </div>
                    </section>
                </main>
            </div>
        </>
    );
}
