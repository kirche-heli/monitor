import re
from datetime import datetime, timezone
import zoneinfo

tz = zoneinfo.ZoneInfo("Europe/Berlin")

with open("kalender_raw.ics", "r", encoding="utf-8", errors="replace") as f:
    content = f.read()

def utc_to_berlin(m):
    pre, s = m.group(1), m.group(2)
    try:
        dt = datetime(
            int(s[0:4]), int(s[4:6]), int(s[6:8]),
            int(s[9:11]), int(s[11:13]), int(s[13:15]),
            tzinfo=timezone.utc
        ).astimezone(tz)
        return pre + ";TZID=Europe/Berlin:" + dt.strftime("%Y%m%dT%H%M%S")
    except Exception:
        return m.group(0)

result = re.sub(r"(DTSTART|DTEND):(\d{8}T\d{6})Z", utc_to_berlin, content)

count = len(re.findall(r"DTSTART:.*?Z", content))
print("Umgerechnet:", count, "UTC-Termine")

with open("kalender.ics", "w", encoding="utf-8") as f:
    f.write(result)

print("kalender.ics gespeichert")
