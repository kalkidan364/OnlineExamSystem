
import re

files = [
    r"src/modules/admin/pages/ActivityLogs.vue",
    r"src/modules/department-head/pages/ActivityLogs.vue"
]

for file in files:
    with open(file, "r", encoding="utf-8") as f:
        content = f.read()

    # Find the overflow-x-auto wrapper and remove overflow-x-auto
    content = content.replace("<div class=\"overflow-x-auto flex-1\">", "<div class=\"flex-1\">")
    
    # Also, some columns like Description have whitespace-nowrap or truncate, which is good. Let us make sure Time, User, Role, Action, Module, Description, IP Address, Status, Actions fit.
    # Actually just removing overflow-x-auto prevents scrolling.
    
    with open(file, "w", encoding="utf-8") as f:
        f.write(content)
        print(f"Updated {file}")

