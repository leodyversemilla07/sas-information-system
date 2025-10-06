import { Head } from '@inertiajs/react';
import Header from '@/components/usg/header';
import Footer from '@/components/usg/footer';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

export default function UsgLanding() {
    // Color palette taken from USG logo: deep green, medium green, gold
    const palette = {
        bg: '#F6FBF6', // very light green background
        deepGreen: '#0f5b2f', // dark green (text / header)
        midGreen: '#2ea24a', // mid green (accent)
        gold: '#C49A2A', // gold for highlights / buttons
        whiteCard: '#ffffff'
    };

    return (
        <>
            <Head title="USG Transparency Portal" />

            <Header />

            <div className="min-h-screen p-6 lg:p-12" style={{ backgroundColor: palette.bg, color: palette.deepGreen }}>


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
                                <a href="/usg/announcements">
                                    <Button className="text-white" style={{ backgroundColor: palette.gold, borderColor: palette.gold }}>
                                        Explore Announcements
                                    </Button>
                                </a>
                                <a href="/usg/calendar">
                                    <Button variant="secondary" className="border" style={{ borderColor: palette.midGreen, color: palette.midGreen }}>
                                        View Calendar
                                    </Button>
                                </a>
                            </div>
                        </div>

                        <Card style={{ backgroundColor: palette.whiteCard }}>
                            <CardHeader>
                                <CardTitle>Key features</CardTitle>
                            </CardHeader>
                            <CardContent>
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
                            </CardContent>
                        </Card>
                    </section>

                    <section className="mt-12 grid gap-6 lg:grid-cols-3">
                        <div className="rounded-lg p-6 shadow" style={{ backgroundColor: palette.whiteCard, borderTop: `4px solid ${palette.midGreen}` }}>
                            <h4 className="mb-2 font-medium" style={{ color: palette.deepGreen }}>About</h4>
                            <p className="text-sm" style={{ color: '#3b5660' }}>
                                The USG Transparency Portal offers students and stakeholders
                                an easy way to review governance activity and participate in
                                campus life.
                            </p>
                        </div>

                        <div className="rounded-lg p-6 shadow" style={{ backgroundColor: palette.whiteCard, borderTop: `4px solid ${palette.midGreen}` }}>
                            <h4 className="mb-2 font-medium" style={{ color: palette.deepGreen }}>Contact</h4>
                            <p className="text-sm" style={{ color: '#3b5660' }}>usg@minsubongabong.edu.ph</p>
                            <p className="text-sm" style={{ color: '#3b5660' }}>Office: Student Center, 2nd Floor</p>
                        </div>

                        <div className="rounded-lg p-6 shadow" style={{ backgroundColor: palette.whiteCard, borderTop: `4px solid ${palette.midGreen}` }}>
                            <h4 className="mb-2 font-medium" style={{ color: palette.deepGreen }}>Accessibility</h4>
                            <p className="text-sm" style={{ color: '#3b5660' }}>
                                This page follows the site's look and supports mobile,
                                tablet, and desktop layouts.
                            </p>
                        </div>
                    </section>
                </main>
            </div>

            <Footer />
        </>
    );
}
