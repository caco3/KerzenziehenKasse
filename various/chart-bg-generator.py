### Script to generate the background image for the diagrams (statsDiagrams.php)
###

from PIL import Image, ImageDraw

divWidth = 1600
divHeight = 600

transparency = 180


dayNames = ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa", "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"]
days = len(dayNames)


# top, right, bottom, left (like in css, https://www.w3schools.com/css/css_padding.asp)
chartPadding = (20, 120, 40, 100)

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


print("x1, y1, x2, y2")
for day in range(days):
    x1 = chartPadding[left] + day * dayWidth
    x2 = x1 + dayWidth
    
    y1 = chartPadding[top]
    y2 = chartPadding[top]+dayHeight 
    print(day, dayNames[day], x1, y1, x2, y2)
    
    if dayNames[day] == "So": # sundays
        color = (255, 200, 200, transparency)
    elif (dayNames[day] == "Mo") or (dayNames[day] == "Mi") or (dayNames[day] == "Fr"): # Mo, Mi, Fr
        color = (218, 218, 218, transparency)
    else: # Di, Do
        color = (255, 255, 255, transparency)

    draw.rectangle([x1, y1, x2, y2], outline=None, fill=color)



# save image
f = '../src/images/chart-bg.png'
print("Generated %s" % f)
im.save(f) 
