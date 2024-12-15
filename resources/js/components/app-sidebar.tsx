"use client";

import * as React from "react";
import { Circle, Grid, Layout, Utensils, Speaker } from "lucide-react";

import { NavUser } from "@/components/nav-user";
import { TeamSwitcher } from "@/components/team-switcher";
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarRail,
} from "@/components/ui/sidebar";

const data = {
    user: {
        name: "Event Planner",
        email: "planner@example.com",
        avatar: "/avatars/planner.jpg",
    },
    teams: [
        { name: "Event Team A", logo: Grid, plan: "Pro" },
        { name: "Event Team B", logo: Layout, plan: "Free" },
    ],
    navMain: [
        {
            title: "Seating Options",
            url: "#",
            icon: Circle,
            isActive: true,
            items: [
                {
                    title: "Round Table",
                    url: "#",
                    elementName: "seatContainerRound",
                    type: "seatContainerRound",
                },
                {
                    title: "Table",
                    url: "#",
                    elementName: "seatContainerTable",
                    type: "seatContainerTable",
                },
                {
                    title: "Seat Container",
                    url: "#",
                    elementName: "seatContainerRect",
                    type: "seatContainerRect",
                },
            ],
        },
        {
            title: "Other Elements",
            url: "#",
            icon: Utensils,
            items: [
                {
                    title: "Speaker Area",
                    url: "#",
                    elementName: "speakerArea",
                    type: "speakerArea",
                },
                {
                    title: "Food Area",
                    url: "#",
                    elementName: "foodArea",
                    type: "foodArea",
                },
                {
                    title: "Restroom",
                    url: "#",
                    elementName: "restroom",
                    type: "restroom",
                },
                {
                    title: "Stage",
                    url: "#",
                    elementName: "stage",
                    type: "stage",
                },
            ],
        },
    ],
};

export function AppSidebar(
    {
        onDragStart,
        ...props
    }: { onDragStart: (element: string) => void } & React.ComponentProps<
        typeof Sidebar
    >,
    eventData) {
    return (
        <Sidebar collapsible="icon" {...props}>
            <SidebarHeader>
                <div className="px-4 py-2 border-b border-gray-300 bg-gray-100">
                    <h1 className="text-lg font-semibold text-gray-800">
                        Editor 
                    </h1>
                    <p className="text-sm text-gray-600">
                        Customize your layout by selecting and editing elements
                        from the sidebar.
                    </p>
                </div>
            </SidebarHeader>
            <SidebarContent>
                {data.navMain.map((group) => (
                    <div key={group.title}>
                        <h4 className="px-4 py-2 text-sm font-medium text-gray-700">
                            {group.title}
                        </h4>
                        <div className="flex flex-col gap-2 px-4">
                            {group.items.map((item) => (
                                <div
                                    key={item.title}
                                    className="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-gray-200"
                                    draggable
                                    onDragStart={(e) => {
                                        // Pass element name via dataTransfer
                                        e.dataTransfer.setData(
                                            "text/plain",
                                            item.elementName
                                        );
                                        onDragStart(item.elementName);
                                    }}
                                >
                                    <span className="text-sm text-gray-700">
                                        {item.title}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>
                ))}
            </SidebarContent>
            <SidebarFooter>
                <NavUser user={eventData} />
            </SidebarFooter>
            <SidebarRail />
        </Sidebar>
    );
}
