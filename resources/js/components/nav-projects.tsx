"use client";

import {
    SidebarGroup,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/components/ui/sidebar";
import React from "react";

export function NavProjects({
    projects,
}: {
    projects: { name: string; url: string; icon: any }[];
}) {
    return (
        <SidebarGroup>
            <SidebarMenu>
                {projects.map((project) => (
                    <SidebarMenuItem key={project.name}>
                        <SidebarMenuButton tooltip={project.name}>
                            {project.icon && <project.icon />}
                            <span>{project.name}</span>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                ))}
            </SidebarMenu>
        </SidebarGroup>
    );
}
