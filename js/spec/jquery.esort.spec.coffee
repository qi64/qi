describe "esort", ->
  fixture = '#esort-ul-fixture'
  shouldBeSorted = (a = 'a', c = 'c')->
    expect($(fixture).find 'li:first').toHaveText(a)
    expect($(fixture).find('li').not(':first').not(':last').text()).toEqual(' b')
    expect($(fixture).find 'li:last' ).toHaveText(c)

  beforeEach ->
    loadFixtures "esort.fixture.html"

  it "Should Sort children and trim spaces", ->
    $(fixture).esort()
    shouldBeSorted()

  it "Should Sort Reverse", ->
    $(fixture).esort reverse:true
    shouldBeSorted('c', 'a')
