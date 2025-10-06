import { Link } from '@inertiajs/react';
import { Facebook, Instagram, Twitter, Youtube, MapPin, Mail, Phone } from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';

export default function Footer() {
    const quickLinks = [
        { name: 'Home', href: '/usg' },
        { name: 'Officers', href: '/usg/officers' },
        { name: 'Announcements', href: '/usg/announcements' },
        { name: 'Calendar', href: '/usg/calendar' },
        { name: 'Transparency Reports', href: '/usg/transparency/reports' },
        { name: 'Feedback / Contact', href: '/usg/feedback' }
    ];

    const socialLinks = [
        { name: 'Facebook', icon: Facebook, href: '#', color: 'hover:text-blue-400' },
        { name: 'Instagram', icon: Instagram, href: '#', color: 'hover:text-pink-400' },
        { name: 'Twitter', icon: Twitter, href: '#', color: 'hover:text-blue-300' },
        { name: 'YouTube', icon: Youtube, href: '#', color: 'hover:text-red-400' }
    ];

    return (
        <footer className="bg-gradient-to-r from-green-800 to-green-900 text-white">
            {/* Main Footer */}
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    {/* About USG Section */}
                    <div className="space-y-4">
                        <div className="flex items-center gap-3">
                            <AppLogoIcon className="w-8 h-8 fill-white" />
                            <img
                                src="/usg-logo.png"
                                alt="USG Logo"
                                className="w-8 h-8 rounded-full object-cover"
                            />
                        </div>
                        <div>
                            <h3 className="text-lg font-semibold mb-2">Mindoro State University — Bongabong Campus</h3>
                            <p className="text-sm text-green-100 mb-2">University Student Government</p>
                            <p className="text-sm text-gray-300 leading-relaxed">
                                Empowering the student body through transparent governance, leadership, and service.
                            </p>
                        </div>
                    </div>

                    {/* Quick Links Section */}
                    <div className="space-y-4">
                        <h3 className="text-lg font-semibold">Quick Links</h3>
                        <ul className="space-y-2">
                            {quickLinks.map((link) => (
                                <li key={link.name}>
                                    <Link
                                        href={link.href}
                                        className="text-sm text-gray-300 hover:text-white transition-colors duration-200"
                                    >
                                        {link.name}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>

                    {/* Contact Information Section */}
                    <div className="space-y-4">
                        <h3 className="text-lg font-semibold">Contact Information</h3>
                        <div className="space-y-3">
                            <div className="flex items-start gap-3">
                                <MapPin size={16} className="text-green-300 mt-1 flex-shrink-0" />
                                <div className="text-sm text-gray-300">
                                    <p className="font-medium">Office of the University Student Government</p>
                                    <p>Mindoro State University, Bongabong Campus</p>
                                    <p>Oriental Mindoro, Philippines</p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <Mail size={16} className="text-green-300 flex-shrink-0" />
                                <a
                                    href="mailto:usg@minsu.edu.ph"
                                    className="text-sm text-gray-300 hover:text-white transition-colors duration-200"
                                >
                                    usg@minsu.edu.ph
                                </a>
                            </div>
                            <div className="flex items-center gap-3">
                                <Phone size={16} className="text-green-300 flex-shrink-0" />
                                <span className="text-sm text-gray-300">+63 912 345 6789</span>
                            </div>
                        </div>
                    </div>

                    {/* Social Media Section */}
                    <div className="space-y-4">
                        <h3 className="text-lg font-semibold">Follow Us</h3>
                        <div className="flex gap-4">
                            {socialLinks.map((social) => {
                                const Icon = social.icon;
                                return (
                                    <a
                                        key={social.name}
                                        href={social.href}
                                        className={`text-gray-300 ${social.color} transition-colors duration-200`}
                                        aria-label={social.name}
                                    >
                                        <Icon size={24} />
                                    </a>
                                );
                            })}
                        </div>
                        <p className="text-sm text-gray-400 mt-4">
                            Stay connected with the latest updates and announcements from the USG.
                        </p>
                    </div>
                </div>
            </div>

            {/* Sub-Footer */}
            <div className="border-t border-green-700 bg-green-900">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
                    <div className="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div className="text-sm text-gray-300 text-center md:text-left">
                            © 2025 Mindoro State University — Bongabong Campus | University Student Government. All rights reserved.
                        </div>
                        <div className="text-sm text-gray-400 text-center md:text-right">
                            Developed by the USG Digital Committee
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    );
}
