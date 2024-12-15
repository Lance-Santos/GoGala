        "use client";

        import * as React from "react";

        import {
            Sidebar,
            SidebarContent,
            SidebarGroup,
            SidebarGroupContent,
            SidebarGroupLabel,
            SidebarRail,
        } from "@/components/ui/sidebar";
        import { Input } from "@/components/ui/input";
        import { Label } from "@/components/ui/label";
        import { Separator } from "@/components/ui/separator";
        import { Slider } from "@/components/ui/slider";

        export default function AppSidebar({
            ...props
        }: React.ComponentProps<typeof Sidebar>) {
            return (
                <Sidebar {...props}>
                    <SidebarContent>
                        {/* Element Properties Section */}
                        <SidebarGroup>
                            <SidebarGroupLabel>Element Properties</SidebarGroupLabel>
                            <div className="m-2">
                                <SidebarGroupContent>
                                    <div className="space-y-4">
                                        {/* Element Name */}
                                        <div className="grid w-full max-w-sm items-center gap-1.5">
                                            <Label htmlFor="element-name">
                                                Element Name
                                            </Label>
                                            <Input
                                                type="text"
                                                id="element-name"
                                                placeholder="e.g., Round Table"
                                            />
                                        </div>
                                        {/* Coordinates */}
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <Label htmlFor="x-coord">
                                                    X Coordinate
                                                </Label>
                                                <Input
                                                    type="number"
                                                    id="x-coord"
                                                    placeholder="e.g., 120"
                                                />
                                            </div>
                                            <div>
                                                <Label htmlFor="y-coord">
                                                    Y Coordinate
                                                </Label>
                                                <Input
                                                    type="number"
                                                    id="y-coord"
                                                    placeholder="e.g., 240"
                                                />
                                            </div>
                                        </div>
                                        {/* Rotation */}
                                        <div className="grid w-full max-w-sm items-center gap-1.5">
                                            <Label htmlFor="rotation">
                                                Rotation (degrees)
                                            </Label>
                                            <Slider
                                                defaultValue={[0]}
                                                max={360}
                                                step={1}
                                            />
                                        </div>
                                        {/* Number of Seats */}
                                        <div className="grid w-full max-w-sm items-center gap-1.5">
                                            <Label htmlFor="seats">
                                                Number of Seats
                                            </Label>
                                            <Input
                                                type="number"
                                                id="seats"
                                                placeholder="e.g., 8"
                                            />
                                        </div>
                                    </div>
                                </SidebarGroupContent>
                            </div>
                        </SidebarGroup>

                        <Separator />

                        {/* Seat Positioning Section */}
                        <SidebarGroup>
                            <SidebarGroupLabel>Seat Positioning</SidebarGroupLabel>
                            <SidebarGroupContent className="m-2">
                                <div className="space-y-4">
                                    {/* Top Seats */}
                                    <div className="grid w-full max-w-sm items-center gap-1.5">
                                        <Label htmlFor="top-seats">Top Seats</Label>
                                        <Input
                                            type="number"
                                            id="top-seats"
                                            placeholder="e.g., 2"
                                        />
                                    </div>
                                    {/* Left Seats */}
                                    <div className="grid w-full max-w-sm items-center gap-1.5">
                                        <Label htmlFor="left-seats">Left Seats</Label>
                                        <Input
                                            type="number"
                                            id="left-seats"
                                            placeholder="e.g., 3"
                                        />
                                    </div>
                                    {/* Right Seats */}
                                    <div className="grid w-full max-w-sm items-center gap-1.5">
                                        <Label htmlFor="right-seats">Right Seats</Label>
                                        <Input
                                            type="number"
                                            id="right-seats"
                                            placeholder="e.g., 3"
                                        />
                                    </div>
                                    {/* Bottom Seats */}
                                    <div className="grid w-full max-w-sm items-center gap-1.5">
                                        <Label htmlFor="bottom-seats">
                                            Bottom Seats
                                        </Label>
                                        <Input
                                            type="number"
                                            id="bottom-seats"
                                            placeholder="e.g., 2"
                                        />
                                    </div>
                                </div>
                            </SidebarGroupContent>
                        </SidebarGroup>
                    </SidebarContent>
                    <SidebarRail />
                </Sidebar>
            );
        }
