
import re

files = [
    r"src/modules/admin/pages/ActivityLogs.vue",
    r"src/modules/department-head/pages/ActivityLogs.vue"
]

for file in files:
    with open(file, "r", encoding="utf-8") as f:
        content = f.read()

    # Find the cards part (now labeled "Top Cards")
    cards_start = content.find("<!-- Top Cards -->")
    cards_end = content.find("<!-- Left: Log Table -->")
    
    # Find the table part
    table_start = content.find("<!-- Left: Log Table -->")
    table_end = content.rfind("</div>\n  </div>\n</template>")
    
    if cards_start != -1 and table_start != -1:
        cards_html = content[cards_start:cards_end]
        table_html = content[table_start:table_end]
        
        # Change label
        cards_html = cards_html.replace("<!-- Top Cards -->", "<!-- Bottom Cards -->")
        table_html = table_html.replace("<!-- Left: Log Table -->", "<!-- Top: Log Table -->")
        
        before_main = content[:content.find("<!-- Main Content + Sidebar Grid -->")]
        
        new_main = "<!-- Main Content + Sidebar Grid -->\n    <div class=\"flex flex-col gap-6\">\n\n      " + table_html + "      " + cards_html + "    </div>\n  </div>\n</template>\n"
        
        with open(file, "w", encoding="utf-8") as f:
            f.write(before_main + new_main)
            print(f"Updated {file}")

