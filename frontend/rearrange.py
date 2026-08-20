
import re

files = [
    r"src/modules/admin/pages/ActivityLogs.vue",
    r"src/modules/department-head/pages/ActivityLogs.vue"
]

for file in files:
    with open(file, "r", encoding="utf-8") as f:
        content = f.read()

    # Find the table part
    table_start = content.find("<!-- Left: Log Table -->")
    table_end = content.find("<!-- Right Sidebar -->")
    
    # Find the sidebar part
    sidebar_start = content.find("<!-- Right Sidebar -->")
    sidebar_end = content.rfind("</div>\n  </div>\n</template>")
    
    if table_start != -1 and sidebar_start != -1:
        table_html = content[table_start:table_end]
        sidebar_html = content[sidebar_start:sidebar_end]
        
        # Modify table html to take full width
        table_html = table_html.replace("<div class=\"flex-1 min-w-0 bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col h-[fit-content]\">", "<div class=\"w-full bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col h-[fit-content]\">")
        
        # Modify sidebar html to be a grid
        sidebar_html = sidebar_html.replace("<div class=\"w-[280px] shrink-0 space-y-6\">", "<div class=\"grid grid-cols-1 lg:grid-cols-3 gap-6\">")
        sidebar_html = sidebar_html.replace("<!-- Right Sidebar -->", "<!-- Top Cards -->")
        
        # Replace the whole main content
        before_main = content[:content.find("<!-- Main Content + Sidebar Grid -->")]
        
        new_main = "<!-- Main Content + Sidebar Grid -->\n    <div class=\"flex flex-col gap-6\">\n\n      " + sidebar_html + "      " + table_html + "    </div>\n  </div>\n</template>\n"
        
        with open(file, "w", encoding="utf-8") as f:
            f.write(before_main + new_main)
            print(f"Updated {file}")

