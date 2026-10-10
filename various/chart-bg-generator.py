### Script to generate the background image for the diagrams (statsDiagrams.php)
###

import os
from PIL import Image, ImageDraw

# Matches the chart container width in statsDiagrams.php
divWidth = 1600+84
divHeight = 600

transparency = 180


dayNames = ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa", "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"]
days = len(dayNames)


# top, right, bottom, left (like in css, https://www.w3schools.com/css/css_padding.asp)
chartPadding = (0, 2, 30, 0)

#index
left = 3
right = 1
top = 0
bottom = 2


# size of image
canvas = (divWidth, divHeight)

# Chart
chartWidth = divWidth - chartPadding[left] - chartPadding[right]
chartHeight = divHeight - chartPadding[top] - chartPadding[bottom]

dayWidth = chartWidth / days
dayHeight = chartHeight

# init canvas
im = Image.new('RGBA', canvas, (255, 255, 255, 0))
draw = ImageDraw.Draw(im)


# main frame (testing)
# x1, y1, x2, y2
#draw.rectangle([chartPadding[left], divHeight-chartPadding[bottom]-1, chartPadding[left]+chartWidth, chartPadding[top]], outline=(255, 0, 0, 255))


lineWidth = 2

bottomY = chartPadding[top] + chartHeight
extensionHeight = 50
lineBottomY = bottomY + extensionHeight

print("x1, y1, x2, y2")
for day in range(days):
    x1 = chartPadding[left] + day * dayWidth
    x2 = x1 + dayWidth

    y1 = chartPadding[top]
    y2 = bottomY
    print(day, dayNames[day], x1, y1, x2, y2)

    if dayNames[day] == "So": # sundays
        color = (255, 200, 200, transparency)
    elif (dayNames[day] == "Mo") or (dayNames[day] == "Mi") or (dayNames[day] == "Fr"): # Mo, Mi, Fr
        color = (218, 218, 218, transparency)
    else: # Di, Do
        color = (255, 255, 255, transparency)

    draw.rectangle([x1, y1, x2, y2], outline=None, fill=color)

# Sundays get a red extension below the main chart area.
for day in range(days):
    if dayNames[day] != "So":
        continue
    x1 = chartPadding[left] + day * dayWidth
    x2 = x1 + dayWidth
    draw.rectangle([x1, bottomY, x2, lineBottomY], outline=None, fill=(255, 200, 200, transparency))

# Draw the main horizontal line at the bottom of the main chart area in gray,
# crossing all columns.
draw.line(
    [(chartPadding[left], bottomY), (chartPadding[left] + chartWidth, bottomY)],
    fill=(128, 128, 128, 255),
    width=lineWidth
)

# Draw vertical black lines between day columns on top of everything,
# all the way down to the bottom of the Sunday extensions, including the
# outer edges of the first and last columns.
for day in range(days + 1):
    x = chartPadding[left] + day * dayWidth
    draw.line([(x, chartPadding[top]), (x, lineBottomY)], fill=(0, 0, 0, 255), width=lineWidth)


# save image relative to this script, so it works from any CWD
out_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', 'src', 'images'))
os.makedirs(out_dir, exist_ok=True)
f = os.path.join(out_dir, 'chart-bg.png')
print("Generated %s" % f)
im.save(f) 
