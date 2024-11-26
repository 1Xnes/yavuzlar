package main

import (
	"fmt"
	"os"

	"github.com/gocolly/colly"
)

/*
Scrape edilecek siteler:
https://thehackernews.com/
https://www.scrapethissite.com/pages/
https://webscraper.io/test-sites/e-commerce/allinone
*/

type NewsStructure struct {
	Title       string
	Date        string
	Description string
}

var scrapesiteList = []string{}
var webscrapesitelist = []string{}

var news = []NewsStructure{}
var running = true

func main() {
	running = true
	for running {
		mainMenu()
	}

}

func mainMenu() {
	selectValue := 0
	fmt.Println("Ana Menüye Hoşgeldin")
	fmt.Println("-1 : HackerNews'i Scrape Et")
	fmt.Println("-2 : Scrapethissite'ı Scrape Et")
	fmt.Println("-3 : Webscraper'ın Test Sitesini Scrape Et")
	fmt.Println("-4 : Çıkış Yap")
	fmt.Scanln(&selectValue)
	switch selectValue {
	case -1:
		hackernewsPreparer()
		HackerNewsScrape()
	case -2:
		ScrapethissiteScrape()
	case -3:
		WebscraperScrape()
	case -4:
		fmt.Println("Çıkış Yapılıyor...")
		running = false
	default:
		fmt.Println("Geçersiz Seçim Yaptınız")
	}
}

func hackernewsPreparer() {
	// önden bi kere çalıştırıyorum ki access denied 403 almayayım Hocam ancak böyle çözebildim sorunumu (tahminim user agent set etmediğim için oluyor ama bende çalışıyor böyle)
	c := colly.NewCollector(
		// Visit only domain : thehackernews.com
		colly.AllowedDomains("thehackernews.com"),
	)
	c.OnHTML(".clear.home-post-box.cf", func(e *colly.HTMLElement) {
	})
	c.OnRequest(func(r *colly.Request) {
		//fmt.Println("Visiting", r.URL.String())
	})
	c.OnError(func(r *colly.Response, err error) {
		//fmt.Println("Request URL:", r.Request.URL, "failed with response:", r, "\nError:", err)
	})
	c.Visit("http://thehackernews.com/")
}

func HackerNewsScrape() {
	c := colly.NewCollector(
		// Visit only domain : thehackernews.com
		colly.AllowedDomains("thehackernews.com"),
	)

	//on every element which has <h2 class="home-title"> call callback
	// test koduydu, alttaki koda çevirdim kodlar direk dökümantasyondan alıntı Hocam
	/*
		c.OnHTML("h2.home-title", func(e *colly.HTMLElement) {
			// Print title
			fmt.Printf("Title found: %q\n", e.Text)
		})
	*/

	//on every element which has class="clear home-post-box cf" call callback
	c.OnHTML(".clear.home-post-box.cf", func(e *colly.HTMLElement) {
		// Save news to array
		news = append(news, NewsStructure{
			Title:       e.ChildText("h2.home-title"),
			Date:        e.ChildText(".h-datetime"),
			Description: e.ChildText(".home-desc"),
		})

		saveNewsToFile(news)
	})

	// Before making a request print "Visiting ..."
	c.OnRequest(func(r *colly.Request) {
		fmt.Println("Visiting", r.URL.String())
	})

	c.OnError(func(r *colly.Response, err error) {
		fmt.Println("Request URL:", r.Request.URL, "failed with response:", r, "\nError:", err)
	})

	// Start scraping on https://thehackernews.com
	c.Visit("http://thehackernews.com/")
}

func ScrapethissiteScrape() {
	c := colly.NewCollector(
		// Visit only domain : www.scrapethissite.com/pages/
		colly.AllowedDomains("www.scrapethissite.com"),
	)
	//on every element which has <div class="page"> call callback
	c.OnHTML("div.page", func(e *colly.HTMLElement) {
		// Save each title and description to array
		scrapesiteList = append(scrapesiteList, e.ChildText("h3.page-title a"))
		scrapesiteList = append(scrapesiteList, e.ChildText("p.lead.session-desc"))
		//fmt.Println(scrapesiteList)

	})

	// Before making a request print "Visiting ..."
	c.OnRequest(func(r *colly.Request) {
		fmt.Println("Visiting", r.URL.String())
	})

	c.OnError(func(r *colly.Response, err error) {
		fmt.Println("Request URL:", r.Request.URL, "failed with response:", r, "\nError:", err)
	})

	// Start scraping
	c.Visit("http://www.scrapethissite.com/pages/")
	saveScrapesiteToFile(scrapesiteList)
}

func WebscraperScrape() {
	c := colly.NewCollector(
		// Visit only domain : webscraper.io
		colly.AllowedDomains("webscraper.io"),
	)
	//on every element which has <div class="product-wrapper card-body"> call callback
	c.OnHTML(".product-wrapper.card-body", func(e *colly.HTMLElement) {
		// Save each title price and description to array
		webscrapesitelist = append(webscrapesitelist, e.ChildText("h4 a.title"))
		webscrapesitelist = append(webscrapesitelist, e.ChildText("h4.price"))
		webscrapesitelist = append(webscrapesitelist, e.ChildText(".description"))
	})

	// Before making a request print "Visiting ..."
	c.OnRequest(func(r *colly.Request) {
		fmt.Println("Visiting", r.URL.String())
	})

	c.OnError(func(r *colly.Response, err error) {
		fmt.Println("Request URL:", r.Request.URL, "failed with response:", r, "\nError:", err)
	})

	// Start scraping
	c.Visit("https://webscraper.io/test-sites/e-commerce/allinone")
	saveWebscraperToFile(webscrapesitelist)
}

func saveNewsToFile(news []NewsStructure) {
	file, err := os.OpenFile("dailyNews.txt", os.O_WRONLY|os.O_TRUNC|os.O_CREATE, 0644)
	if err != nil {
		fmt.Println(err)
	}
	defer file.Close()
	// write each new to file one by one
	for _, n := range news {
		//this for removing the calendar sign from variable
		if n.Date != "" {
			n.Date = n.Date[3:]
		}
		file.WriteString("Başlık: " + n.Title + "\n")
		file.WriteString("Tarih: " + n.Date + "\n")
		file.WriteString("Açıklama: " + n.Description + "\n")
		file.WriteString("\n\n")
	}
}

func saveScrapesiteToFile(scrapesiteList []string) {
	file, err := os.OpenFile("scrapesiteList.txt", os.O_WRONLY|os.O_TRUNC|os.O_CREATE, 0644)
	if err != nil {
		fmt.Println(err)
	}
	defer file.Close()
	// write each page and description to file two by two
	for i := 0; i < len(scrapesiteList); i += 2 {
		file.WriteString("Sayfa: " + scrapesiteList[i] + "\n")
		file.WriteString("Açıklama: " + scrapesiteList[i+1] + "\n")
		file.WriteString("\n\n")
	}
}

func saveWebscraperToFile(webscrapesitelist []string) {
	file, err := os.OpenFile("webscraper_products.txt", os.O_WRONLY|os.O_TRUNC|os.O_CREATE, 0644)
	if err != nil {
		fmt.Println(err)
	}
	defer file.Close()

	// write each product to file three by three
	for i := 0; i < len(webscrapesitelist); i += 3 {
		file.WriteString("Ürün Adı: " + webscrapesitelist[i] + "\n")
		file.WriteString("Fiyat: " + webscrapesitelist[i+1] + "\n")
		file.WriteString("Açıklama: " + webscrapesitelist[i+2] + "\n")
		file.WriteString("\n\n")
	}
}
